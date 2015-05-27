<?php

/**
 * @property User             $parent
 *
 * @property Client[]         $clients
 * @property Message[]        $incomingMessages
 * @property Message[]        $outgoingMessages
 * @property Product[]        $products
 * @property Shop[]           $shops
 * @property Transfer[]       $transfers
 * @property Warehouse[]      $warehouses
 *
 * @property int              $id
 * @property string           $username
 * @property string           $password
 * @property string           $full_name
 * @property string           $avatar
 * @property string           $role
 * @property string           $phone
 * @property string           $address
 * @property string           $email
 * @property int              $lft
 * @property int              $rgt
 * @property int              $level
 * @property int              $shop_id
 * @property int              $supplier_id
 * @property string           $ip
 *
 * @property Comment[]        $comments
 * @property Inventory[]      $inventories
 * @property Message[]        $messages
 * @property Message[]        $messages1
 * @property Notification[]   $notifications
 * @property Supplier         $supplier
 * @property Supplier[]       $suppliers
 * @property Transaction[]    $transactions
 * @property Shop             $shop
 * @property Int              $topTransactionSum
 *
 * @property string           $childRole
 * @method User descendants
 * @method User descendantsOf
 * @method User children
 * @method User childrenOf
 */
class User extends ActiveRecord
{

    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_MANAGER = 'manager';
    const ROLE_CASHIER = 'cashier';
    const ROLE_SELLER = 'seller';

    public $remember = false;

    public $cropParams;

    public $deleteAvatar;

    public $availableShops;
    public $availableSuppliers;

    /**
     * @var UserIdentity
     */
    private $_identity;

    /**
     * @return string
     */
    public function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            'nestedSet' => [
                'class' => 'NestedSetBehavior',
            ]
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['full_name, role, phone, address, email', 'required', 'on' => 'create, update'],
            ['phone, email', 'length', 'max' => 50, 'on' => 'create, update'],
            ['cropParams', 'validateCropSize', 'on' => 'create, update'],
            ['deleteAvatar', 'boolean', 'on' => 'update'],
            ['username, password', 'required', 'on' => 'create'],
            ['username', 'length', 'max' => 50, 'on' => 'create'],
            ['username', 'unique', 'className' => 'User', 'attributeName' => 'username', 'on' => 'create'],
            ['password, full_name, address', 'length', 'max' => 100, 'on' => 'create, update'],
            ['role', 'length', 'max' => 13, 'on' => 'create, update'],
            ['username, password', 'required', 'on' => 'login'],
            ['id', 'identify', 'on' => 'login'],
            ['remember', 'safe', 'on' => 'login'],
            ['supplier_id', 'required', 'on' => 'create:manager'],
            ['supplier_id', 'validateSupplier', 'on' => 'create:manager'],
            ['shop_id', 'required', 'on' => 'create:seller'],
            ['shop_id', 'validateSeller', 'on' => 'create:seller'],
            ['active','numerical', 'on' => 'create, update']
        ];
    }

    public function validateSupplier()
    {
        foreach ($this->availableSuppliers as $supplier) {
            if ($supplier->id == $this->supplier_id) {
                return;
            }
        }

        $this->addError('supplier_id', 'Выбранный поставщик не существует.');
    }

    public function validateSeller()
    {
        foreach ($this->availableShops as $shop) {
            if ($shop->id == $this->shop_id) {
                return;
            }
        }

        $this->addError('shop_id', 'Выбранный магазин не существует.');
    }

    public function getChildRole($role = null)
    {
        if (!$role) {
            $role = $this->role;
        }
        switch ($role) {
            case self::ROLE_ADMINISTRATOR:
                return self::ROLE_SUPERVISOR;
            case self::ROLE_SUPERVISOR:
                return self::ROLE_MANAGER;
            case self::ROLE_MANAGER:
                return [self::ROLE_SELLER, self::ROLE_CASHIER];
            case self::ROLE_CASHIER:
                return self::ROLE_SELLER;
            default:
                throw new Exception('Invalid role detected: ' . $role);
        }
    }

    /**
     * @return User
     */
    public function getSupervisor()
    {
        switch ($this->role) {
            case self::ROLE_SELLER:
                return $this->parent->parent;
            case self::ROLE_MANAGER:
                return $this->parent;
            case self::ROLE_SUPERVISOR:
                return $this;
            default:
                return null;
        }
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'clients' => [self::HAS_MANY, 'Client', 'supplier_id'],
            'comments' => [self::HAS_MANY, 'Comment', 'user_id'],
            'inventories' => [self::HAS_MANY, 'Inventory', 'manager_id'],
            'messages' => [self::HAS_MANY, 'Message', 'sender_id'],
            'messages1' => [self::HAS_MANY, 'Message', 'receiver_id'],
            'notifications' => [self::HAS_MANY, 'Notification', 'user_id'],
            'transactions' => [self::HAS_MANY, 'Transaction', 'seller_id'],
            'transactionsSum' => [self::STAT, 'Transaction', 'seller_id', 'select' => 'sum(sum)'],
            'shop' => [self::BELONGS_TO, 'Shop', 'shop_id'],
            'supplier' => [self::BELONGS_TO, 'Supplier', 'supplier_id'],
            'suppliers' => [self::HAS_MANY, 'Supplier', 'supervisor_id'],
            'shops' => [self::HAS_MANY, 'Shop', ['id' => 'supplier_id'], 'through' => 'suppliers'],
            'warehouses' => [self::HAS_MANY, 'Warehouse', ['id' => 'supplier_id'], 'through' => 'suppliers'],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'full_name' => 'Полное имя',
            'avatar' => 'Фото',
            'role' => 'Роль',
            'phone' => 'Phone',
            'address' => 'Адрес',
            'email' => 'Email',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'level' => 'Level',
            'shop_id' => 'Shop',
            'supplier_id' => 'Бренд',
            'remember' => 'Запомнить меня',
            'active' => 'Активность',
            'ip' => 'IP адрес',

        ];
    }

    public $roleLabels = [
        'administrator' => 'Администратор',
        'supervisor' => 'Руководитель',
        'manager' => 'Менеджер',
        'cashier' => 'Кассир',
        'seller' => 'Продавец'
    ];

    public $pluralRoleLabels = [
        'administrator' => 'Администраторы',
        'supervisor' => 'Руководители',
        'manager' => 'Менеджеры',
        'cashier' => 'Кассиры',
        'seller' => 'Продавцы'
    ];
    
    /**
     * @return CActiveDataProvider
     */
    public function search($criteria)
    {
        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }


    /**
     * @param string $className
     *
     * @return User
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function identify()
    {
        $this->_identity = new UserIdentity($this->username, $this->password);

        if ($this->_identity->authenticate() == false) {
            if ($this->_identity->errorCode == UserIdentity::ERROR_USERNAME_INVALID) {
                $this->addError('username', 'Пользователь не существует.');
            } 
            elseif($this->_identity->errorCode == UserIdentity::ERROR_USERNAME_NOT_ACTIVE){
                $this->addError('username', 'Ваш аккаунт заблокирован');
            } 
            elseif($this->_identity->errorCode == UserIdentity::ERROR_IP_NOT_MATCH) {
                $this->addError('username', 'Вход с данного IP запрещен');
            }
            else {
                $this->addError('password', 'Пароль не правильный.');
            }
        }
    }

    protected function beforeSave()
    {
        UserHelper::updateAvatar($this);

        return parent::beforeSave();
    }


    /**
     * @return \UserIdentity
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    public function validateCropSize()
    {
        if ($this->deleteAvatar == false && $this->cropParams != '') {
            if (preg_match('/^\d+_\d+_\d+_\d+$/', $this->cropParams) !== 1) {
                $this->addError('cropParams', 'Размеры автара указаны неверно');
            }
        }
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (!$values['password']) {
            unset($values['password']);
        }

        parent::setAttributes($values, $safeOnly);
    }

    public static function isSeller($role)
    {
        return $role == self::ROLE_SELLER;
    }
    
    public static function isCashier($role)
    {
        return $role == self::ROLE_CASHIER;
    }

    public static function isManager($role)
    {
        return $role == self::ROLE_MANAGER;
    }

    public static function isSupervisor($role)
    {
        return $role == self::ROLE_SUPERVISOR;
    }

    public static function isAdministrator($role)
    {
        return $role == self::ROLE_ADMINISTRATOR;
    }

    public static function isChild($userId, $childId)
    {
        $user = self::model()->findByPk($userId);

        if (!$user) {
            return false;
        }

        return $user->children()->findByPk($childId) != null;
    }

    public function isScenarioType($type)
    {
        return preg_match('/\:' . $type . '$/', $this->scenario);
    }

    public function getValidators($attribute = null)
    {
        $scenario = $this->scenario;
        $this->scenario = preg_replace('/\:.*$/', '', $scenario);
        $validators = parent::getValidators($attribute);
        $this->scenario = $scenario;

        return $validators;
    }

    public function worksAtShop($shopId)
    {
        $this->dbCriteria->addColumnCondition(['shop_id' => $shopId]);

        return $this;
    }

    public function isOnTop($timeFrom, $timeTo, $topSize)
    {
        $column = new CMysqlColumnSchema();
        $this->metaData->columns['topTransactionSum'] = $column;

        $criteria = new CDbCriteria();
        $criteria->join = 'left join transaction s on s.seller_id = t.id and s.time between :time_from and :time_to';
        $criteria->order = 'topTransactionSum DESC';
        $criteria->group = 't.id';
        $criteria->limit = $topSize;
        $select = ['sum(s.sum) as topTransactionSum'];
        $prefix = $this->tableAlias . '.';
        $schema = $this->dbConnection->commandBuilder->schema;

        foreach ($this->metaData->tableSchema->getColumnNames() as $name) {
            $select[] = $prefix . $schema->quoteColumnName($name);
        }

        $criteria->select = implode(', ', $select);

        $criteria->params = [':time_from' => $timeFrom, ':time_to' => $timeTo];

        $this->dbCriteria->mergeWith($criteria);

        return $this;
    }
}
