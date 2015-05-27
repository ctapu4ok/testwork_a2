<?php

/**
 * @property boolean     $sold
 *
 * @property Promotion[] $matchingPromotions
 *
 * @property Product[]   $products
 */

/**
 * @property int            $id
 * @property int            $seller_id
 * @property int            $client_id
 * @property string         $time
 * @property integer        $draft
 * @property int            $sum
 * @property int            $paid_cash
 * @property int            $paid_credit
 * @property string         $credit_type
 * @property string         $note
 * @property integer        $product_count
 * @property integer        $discount
 * @property int            $shop_id
 * @property int            $transaction_id
 * @property int            $action_id
 *
 * @property Comment[]      $comments
 * @property Sell[]         $sells
 * @property Action         $action
 * @property Client         $client
 * @property Shop           $shop
 * @property Transaction    $transaction
 * @property Transaction[]  $transactions
 * @property User           $seller
 *
 * @property Promotion[]    $matchingPromotions
 */
class Transaction extends ActiveRecord
{

    public $resolvedProducts;

    /** @var  Sell[] */
    public $selectedSells = [];

    /**
     * @return string
     */
    public function tableName()
    {
        return 'transaction';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['action_id', 'default', 'setOnEmpty' => true, 'value' => null],
            ['time', 'date', 'format' => 'yyyy-MM-dd hh:mm:ss'],
            ['credit_type', 'in', 'range' => ['uzcard', 'mastercard', 'visa']],
            ['client_id', 'default', 'setOnEmpty' => true, 'value' => null],
            ['paid_cash, paid_credit', 'filter', 'filter' => [$this, 'removeNumberFormatting']],
            ['paid_cash, paid_credit', 'numerical', 'min' => 0],
            ['selectedSells', 'required', 'message' => 'Выберите по крайней мере один товар.'],
            ['seller_id', 'required', 'message' => 'Выберите продавца.'],
            ['selectedSells', 'validateSells'],
            ['action_id', 'validateAction'],
            ['client_id', 'validateClient'],
            ['id', 'validatePaid'],
            ['seller_id,client_id, shop_id, id, sum, note paid_credit, paid_cash , credit_type, time, product_count', 'safe', 'on' => 'search']
        ];
    }

    public function removeNumberFormatting($string)
    {
        return preg_replace('/[^\-0-9]/', '', $string);
    }

    public function validateClient()
    {
        if ($this->client_id) {
            $this->client = Client::model()->belongsToUser($this->seller)->findByPk($this->client_id);

            if ($this->client == null) {
                $this->addError('client_id', 'Клиент не существует.' . $this->client_id);
            }
        }
    }

    public function validateAction()
    {
        if ($this->action_id) {
            foreach ($this->matchingPromotions as $promotion) {
                foreach ($promotion->actions as $action) {
                    if ($action->id == $this->action_id && $action->type == 'discount') {
                        $this->action = $action;

                        return;
                    }
                }
            }

            $action = Action::model()->findByPk($this->action_id);

            if ($action) {
                $this->addError('action_id', sprintf('Скидка %s неприменима к этой продаже.', $this->action_id));
            } else {
                $this->addError('action_id', sprintf('Скидка #%s не найден.', $this->action_id));
            }
        }
    }

    public function validateSells()
    {
        if (!$this->hasErrors('selectedSells')) {
            $valid = true;
            $sells = [];
            $this->product_count = 0;

            foreach ($this->selectedSells as $key => $selectedSell) {
                $sell = new Sell($this->scenario);
                $sell->attributes = $selectedSell;
                $sell->transaction = $this;
                $sell->setIsNewRecord($sell->id == 0);

                $this->sum += ($sell->product_count * $sell->product->retail_price);
                $this->product_count += 1;

                $sells[$key] = $sell;
            }

            $this->selectedSells = $sells;

            foreach ($this->selectedSells as $selectedSell) {
                $valid = $selectedSell->validate() && $valid;
            }

            if (!$valid) {
                $this->addError('selectedSells', 'Один или несколько выбранных товаров содержат ошибки.');
            }
        }
    }

    public function validatePaid()
    {
        $paid = $this->paid_cash + $this->paid_credit;
        $sum = $this->getTransactionSum();

        if (!$this->draft && $paid < $sum) {
            $this->addError(
                'sum',
                sprintf(
                    'Оплаченная сумма %s меньше общей суммы покупки %s.',
                    Yii::app()->format->formatMoney($paid),
                    Yii::app()->format->formatMoney($sum)
                )
            );
        }
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'comments' => [self::HAS_MANY, 'Comment', 'transaction_id'],
            'sells' => [self::HAS_MANY, 'Sell', 'transaction_id'],
            'action' => [self::BELONGS_TO, 'Action', 'action_id'],
            'client' => [self::BELONGS_TO, 'Client', 'client_id'],
            'shop' => [self::BELONGS_TO, 'Shop', 'shop_id'],
            'transaction' => [self::BELONGS_TO, 'Transaction', 'transaction_id'],
            'transactions' => [self::HAS_MANY, 'Transaction', 'transaction_id'],
            'seller' => [self::BELONGS_TO, 'User', 'seller_id'],
            'products' => [self::MANY_MANY, 'Product', 'sell(transaction_id, product_id)']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seller_id' => 'Продавец',
            'client_id' => 'Клиент',
            'time' => 'Время',
            'draft' => 'Draft',
            'sum' => 'Сумма',
            'paid_cash' => 'Paid Cash',
            'paid_credit' => 'Paid Credit',
            'credit_type' => 'Credit Type',
            'note' => 'Note',
            'product_count' => 'Количество',
            'discount' => 'Discount',
            'shop_id' => 'Магазин',
            'transaction_id' => 'Transaction',
            'action_id' => 'Action',
            'seller.full_name' => 'Продавец',
            'client.full_name' => 'Клиент'
        ];
    }

    /**
     * @param CDbCriteria $criteria
     *
     * @return CActiveDataProvider
     */
    public function search($criteria = null)
    {
        $criteria = $criteria ? : new CDbCriteria();

        $criteria->compare('t.id', $this->id, false);
        $criteria->compare('seller_id', $this->seller_id, true);
        $criteria->compare('client_id', $this->client_id, true);
        $criteria->compare('paid_cash', $this->paid_cash, true);
        $criteria->compare('paid_credit', $this->paid_credit, true);
        $criteria->compare('credit_type', $this->credit_type, true);
        $criteria->compare('note', $this->note, true);
        if(is_array($this->shop_id)){
            $criteria->addInCondition('shop.id',$this->shop_id);
        }
        if (is_array($this->time)) {
            $criteria->compare('time', '>=' . $this->time['from']);
            $criteria->compare('time', '<=' . $this->time['to']);
        }
        if (is_array($this->sum)) {
            $criteria->compare('sum', '>=' . $this->sum['from']);
            $criteria->compare('sum', '<=' . $this->sum['to']);
        }
        if (is_array($this->product_count)) {
            $criteria->compare('product_count', '>=' . $this->product_count['from']);
            $criteria->compare('product_count', '<=' . $this->product_count['to']);
        }

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * @param string $className
     *
     * @return Transaction
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);
    }

    public function beforeSave()
    {
        $this->sum = $this->getTransactionSum();

        return parent::beforeSave();
    }

    protected function afterSave()
    {
        parent::afterSave();

        $ids = [];

        if (!empty($this->selectedSells)) {
            foreach ($this->selectedSells as $sell) {
                if (!$sell->isNewRecord) {
                    $ids[] = $sell->id;
                }
            }
        }

        if (!empty($ids)) {
            $criteria = new CDbCriteria();
            $criteria->addNotInCondition('id', $ids);
            $criteria->addColumnCondition(['transaction_id' => $this->id]);

            /** @var Sell[] $removedSells */
            $removedSells = Sell::model()->findAll($criteria);

            foreach ($removedSells as $sell) {
                $sell->delete();
            }
        }

        if (!empty($this->selectedSells)) {
            foreach ($this->selectedSells as $sell) {
                $sell->transaction_id = $this->id;
                $sell->save(false);

                $ids[] = $sell->id;
            }
        }
    }

    public function draftOnly()
    {
        $this->dbCriteria->mergeWith(['condition' => 't.draft = 1']);

        return $this;
    }

    public function soldBy($sellerId)
    {
        $this->dbCriteria->mergeWith(
            [
                'condition' => 't.seller_id = :seller_id',
                'params' => [':seller_id' => $sellerId]
            ]
        );

        return $this;
    }

    public function restoreSelectedSells()
    {
        $this->selectedSells = $this->sells;
    }

    private $_matchingPromotion = null;

    public function getMatchingPromotions()
    {
        if ($this->_matchingPromotion === null) {

            /** @var Promotion[] $promotions */
            $promotions = Promotion::model()->belongsToUser(Yii::app()->user->model)->findAll();
            $this->_matchingPromotion = [];

            foreach ($promotions as $promotion) {
                if ($promotion->matches($this)) {
                    $this->_matchingPromotion[] = $promotion;
                }
            }
        }

        return $this->_matchingPromotion;
    }

    public function getTransactionSum()
    {
        $sum = $this->getSelectedSellsSum();

        if ($this->action) {
            $sum = floor($sum * (100 - $this->action->discount) / 100);
        }

        return $sum;
    }

    public function getSelectedSellsSum()
    {
        $sum = 0;

        foreach ($this->selectedSells as $sell) {
            $price = $sell->product->retail_price * $sell->product_count;

            if ($sell->action && $sell->action->type != 'gift') {
                $price = floor($price * (100 - $sell->action->discount) / 100);
            }

            $sum += $price;
        }

        return $sum;
    }

    /**
     * @param $user User
     *
     * @return $this
     */
    public function belongsToUser($user)
    {
        switch ($user->role) {
            case 'administrator':
                break;
            case 'supervisor':
                $this->dbCriteria->mergeWith([
                       'with' => ['shop'],
                        'condition' => 'shop.supplier_id = :supplier_id',
                        'params' => ['supplier_id' => array_map(function ($supplier) {
                                return $supplier->id;
                            }, $user->suppliers)]
                    ]);
                break;
            case 'manager':
                $this->dbCriteria->mergeWith([
                       'with' => ['shop'],
                        'condition' => 'shop.supplier_id = :suplier_id',
                        'params' => ['suplier_id' => $user->supplier_id]
                    ]);
                break;
            case 'seller':
                $this->dbCriteria->addColumnCondition(['t.seller_id' => $user->id]);
                break;
            case 'cashier':
                $this->dbCriteria->addColumnCondition(['t.shop_id' => $user->shop_id]);
                break;
        }

        return $this;
    }

    public function belongsToShop($shopId)
    {
        $this->dbCriteria->addColumnCondition(['shop_id' => $shopId]);
        return $this;
    }
    
     public function belongsToClient($clientId)
    {
        $this->dbCriteria->addColumnCondition(['client_id' => $clientId]);
        return $this;
    }

    public function belongsTopSupplier($supplierId)
    {
        if($supplierId){
            $criteria = $this->dbCriteria;
            $criteria->mergeWith(['with' => [
                'shop' => [
                    'condition' => 'shop.supplier_id = :supplier_id',
                    'params' => [':supplier_id' => $supplierId],
                ]
            ]]);
        }

        return $this;
    }

    public function duringThisMonth()
    {
        $dateFrom = new DateTime('first day of this month');
        $dateTo = new DateTime('last day of this month');

        $this->dbCriteria->addBetweenCondition('time', $dateFrom->format('Y-m-d 00:00:00'), $dateTo->format('Y-m-d 23:59:59'));

        return $this;
    }
    
}
