<?php

class TransactionController extends Controller {

    /**
     * @var Transaction
     */
    public $transaction;
    public $pageTitle = 'Продажи';

    public function filters() {
        return [
            ['DataLoaderFilter + update, delete, ', 'loadTransaction'],
            'accessControl'
        ];
    }

    public function accessRules() {
        return [
            ['allow', 'actions' => ['handle', 'draft', 'delete', 'modal', 'query', 'clientQuery', 'sellerQuery', 'clientModal', 'actionModal'], 'roles' => ['createTransaction']],
            ['allow', 'actions' => ['view', 'list', 'export'], 'roles' => ['readTransaction']],
            ['deny']
        ];
    }

    public function actionView($id) {
        $transaction = Transaction::model()->belongsToUser(Yii::app()->user->model)->findByPk($id);

        $this->render('view', ['transaction' => $transaction]);
    }

    public function loadTransaction() {
        if (Yii::app()->user->model->isSeller(Yii::app()->user->model->role)) {
            $transaction = Transaction::model()->soldBy(Yii::app()->user->id)->findByPk(Yii::app()->request->getParam('id'));
        } else {
            $transaction = Transaction::model()->belongsToShop(Yii::app()->user->model->shop_id)->findByPk(Yii::app()->request->getParam('id'));
        }

        if ($transaction === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        $this->transaction = $transaction;
    }

    public function actionHandle($id = null) {
        $user = Yii::app()->user->model;

        if ($id) {
            $this->loadTransaction();
            $transaction = $this->transaction;

            if ($transaction->draft == 0) {
                throw new CHttpException(404, 'Вы не можете изменить оплаченную продажу.');
            }
            $transaction->time = date('Y-m-d h:m:s', time());
        } else {
            $transaction = new Transaction;
        }

        $transaction->shop_id = $user->shop->id;
        $transaction->shop = $user->shop;
        if ($user->isSeller($user->role)) {
            $transaction->seller_id = $user->id;
            $transaction->seller = $user;
        }
        $transaction->draft = 1;

        if (!$transaction->time) {
            $transaction->time = date('Y-m-d H:i:s');
        }

        if (isset($_POST['Transaction'])) {
            $transaction->attributes = $_POST['Transaction'];
            

            if (!$transaction->time) {
                $transaction->time = date('Y-m-d h:m:s', time());
            }
            if (isset($_POST['Transaction']['seller_id']) && !empty($_POST['Transaction']['seller_id'])) {
                $transaction->seller_id = $_POST['Transaction']['seller_id'];
            } else {
                $transaction->seller_id = $user->id;
                $transaction->seller = $user;
            }
            //   print_r($_POST['Transaction']['seller_id']);
            //CVarDumper::dump($_POST,10,true);
            if (!Yii::app()->request->isAjaxRequest) {
                $transaction->draft = 0;

                if ($transaction->validate()) {
                    try {
                        $transaction->save(false);

                        Yii::app()->user->setFlash('success', 'Платеж успешно произведен.');
                        $this->redirect(['view', 'id' => $transaction->id, 'print' => 1]);
                    } catch (Exception $exception) {
                        $transaction->addError('id', $exception->getMessage());
                    }
                }
            } else {
                $transaction->validate();
                $transaction->save(false);

                $promotions = [];

                foreach ($transaction->matchingPromotions as $promotion) {
                    $promotions[] = $promotion->serialize();
                }

                $errors = $transaction->getErrorLabels();
                $sells = [];

                foreach ($transaction->selectedSells as $key => $sell) {
                    $errors = array_merge($errors, $sell->getErrorLabels());
                    $sells[$key] = $sell->serialize();
                }

                $response = [
                    'sells' => $sells,
                    'promotions' => $promotions,
                ];

                if ($errors) {
                    $response['errors'] = $errors;
                }

                if (!$id) {
                    $response['submitUrl'] = $this->createUrl('handle', ['id' => $transaction->id]);
                    $response['deleteUrl'] = $this->createUrl('handle', ['id' => $transaction->id]);
                }

                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }

        if (!$transaction->selectedSells) {
            $transaction->restoreselectedSells();
        }

        $clients = Client::model()->belongsToUser($user)->findAll();
        if ($user->isCashier($user->role) && $user->shop_id != '')
            $sellers = User::model()->findAll("shop_id = " . $user->shop_id . " and role = 'seller'");
        $this->render('handle', ['transaction' => $transaction, 'user' => $user, 'clients' => $clients, 'sellers' => $sellers]);
    }

    public function actionDelete() {
        if ($this->transaction->draft == 0) {
            Yii::app()->user->setFlash('error', 'Вы можете оменить только продажи находящиеся в черновике.');
            $this->redirect(['list']);
        }

        $this->transaction->delete();

        Yii::app()->user->setFlash('success', 'Продажа успешно отменена.');
        $this->redirect(['handle']);
    }

    public function actionExport() {
        ExportHelper::ExportToXlsx(
                $this->actionList(true), [
            ['time', 'Время'],
            ['seller.full_name', 'Продавец'],
            ['client.full_name', 'Клиент'],
            ['product_count', 'Общее кол-во'],
            ['sum', 'Обшая сумма'],
            array(array('article', 'Артикул'), array('barcode', 'Штрихкод'), array('category.name', 'Название'), array('color', 'Цвет'), array('size', 'Размер'), array('sells.product_count', 'Кол-во'))
                ]
        );
    }

    public function actionList($export = null) {

        $user = Yii::app()->user->model;

        $criteria = new CDbCriteria();
        $criteria->with = ['client', 'seller'];
        $criteria->scopes['belongsToUser'] = [$user];
        $criteria->order = 'time desc';
        $criteria->compare('t.draft', 0);

        $my_criteria = new CDbCriteria();
//        echo '<pre>';
//        print_r($_GET['Transaction']);
//        exit;
        $my_criteria->with = ['client', 'seller'];
        $my_criteria->scopes['belongsToUser'] = [$user];
        $my_criteria->order = 'time desc';
        $my_criteria->compare('t.draft', 0);

        $my_criteria->compare('t.id', $_GET['Transaction']['id'], false);
        $my_criteria->compare('seller_id', $_GET['Transaction']['seller_id'], true);
        $my_criteria->compare('client_id', $_GET['Transaction']['client_id'], true);
        $my_criteria->compare('paid_cash', $_GET['Transaction']['paid_cash'], true);
        $my_criteria->compare('paid_credit', $_GET['Transaction']['paid_credit'], true);
        $my_criteria->compare('credit_type', $_GET['Transaction']['credit_type'], true);
        $my_criteria->compare('note', $_GET['Transaction']['note'], true);
        if (is_array($_GET['Transaction']['shop_id'])) {
            $my_criteria->addInCondition('shop.id', $_GET['Transaction']['shop_id']);
        }
        if (is_array($_GET['Transaction']['time'])) {
            $my_criteria->compare('time', '>=' . $_GET['Transaction']['time']['from']);
            $my_criteria->compare('time', '<=' . $_GET['Transaction']['time']['to']);
        }
        if (is_array($_GET['Transaction']['sum'])) {
            $my_criteria->compare('sum', '>=' . $_GET['Transaction']['sum']['from']);
            $my_criteria->compare('sum', '<=' . $_GET['Transaction']['sum']['to']);
        }
        if (is_array($_GET['Transaction']['product_count'])) {
            $my_criteria->compare('product_count', '>=' . $_GET['Transaction']['product_count']['from']);
            $my_criteria->compare('product_count', '<=' . $_GET['Transaction']['product_count']['to']);
        }

        $models = Transaction::model()->findAll($my_criteria);
        $time2 = Transaction::model()->find($my_criteria)->time;
        $my_criteria->order = 'time';
        $time1 = Transaction::model()->find($my_criteria)->time;
        $allCount = 0;
        $allSum = 0;
        $nal = 0;
        $bezNal = 0;
        $tran = 0;
        foreach ($models as $model) {
            $tran++;
            $allCount += (int) $model['product_count'];
            $allSum += (int) $model['sum'];
            $nal += (int) $model['sum'] - (int) $model['paid_credit'];
            $bezNal += (int) $model['paid_credit'];
        }

        $transaction = new Transaction('search');
        $transaction->setAttributes($_GET['Transaction']);
//CVarDumper::dump($transaction->attributes,10,true);        exit();
        $provider = $transaction->search($criteria);
        if ($export) {
            $provider->pagination = false;
            return $provider->data;
        }

        $provider->pagination->pageSize = 50;

        if (Yii::app()->user->checkAccess('createUser')) {
            $sellers = User::model()->descendantsOf($user, null, User::ROLE_SELLER)->findAll();
        }

        if (Yii::app()->user->checkAccess('readShop')) {
            $shops = Shop::model()->belongsToUser($user)->findAll();
        }

        $clients = Client::model()->belongsToUser($user)->findAll();
        $this->render('list', ['dataProvider' => $provider,
            'transaction' => $transaction,
            'sellers' => $sellers,
            'shops' => $shops,
            'clients' => $clients,
            'allCount' => $allCount,
            'time2' => $time2,
            'time1' => $time1,
            'allSum' => $allSum,
            'nal' => $nal,
            'bezNal' => $bezNal,
            'tran'=>$tran,
        ]);
    }

    public function actionDraft() {
        $user = Yii::app()->user->model;
        if (Yii::app()->user->role == 'cashier') {
            $dataProvider = new CActiveDataProvider('Transaction', [
                'criteria' => [
                    //'with' => ['seller', 'client'],
                    'order' => 't.time DESC',
                    'condition' => "shop_id = " . $user->shop_id . " AND draft = 1",
                ],
                'pagination' => ['pageSize' => 50],
            ]);
        } else {
            $dataProvider = new CActiveDataProvider('Transaction', [
                'criteria' => [
                    'with' => ['seller', 'client'],
                    'order' => 't.time DESC',
                    'scopes' => ['draftOnly', 'soldBy' => [Yii::app()->user->id]],
                ],
                'pagination' => ['pageSize' => 50],
            ]);
        }

        $this->render('list_draft', ['dataProvider' => $dataProvider]);
    }

    public function actionModal() {
        $user = Yii::app()->user->model;
        $criteria = new CDbCriteria();
        $criteria->scopes['existInShop'] = [$user->shop_id];
        $criteria->scopes['belongsToUser'] = [Yii::app()->user->model];
        $criteria->with = 'category';

        $product = new Product('search');
        $product->attributes = $_GET;

        $provider = $product->search($criteria);

        $this->renderPartial('modal', ['dataProvider' => $provider]);
    }

    public function actionQuery() {
        $criteria = new CDbCriteria();
        $criteria->scopes['exist'] = [];
        $criteria->scopes['belongsToUser'] = [Yii::app()->user->model];
        $criteria->with = 'category';

        $product = new Product('search');
        $product->attributes = $_GET;

        $provider = $product->search($criteria);

        /** @var Product[] $data */
        $data = $provider->data;
        $response = [];

        foreach ($data as $product) {
            $response[] = [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'barcode' => $product->barcode,
                'color' => $product->color,
                'size' => $product->size,
                'existCount' => $product->existCount,
                'retail_price' => $product->retail_price,
                'category' => $product->category->name,
            ];
        }

        echo json_encode($response);
    }

    public function actionActionModal($actionId) {
        $criteria = new CDbCriteria();
        $criteria->scopes['exist'] = [];
        $criteria->scopes['belongsToUser'] = [Yii::app()->user->model];
        $criteria->with = 'category';

        $product = new Product('search');
        $product->attributes = $_GET;

        /** @var Action $action */
        $action = Action::model()->findByPk($actionId);

        if (!$action) {
            throw new CHttpException(404);
        }

        $action->applyCriteria($criteria);
        $provider = $product->search($criteria);

        $this->renderPartial('article_modal', ['dataProvider' => $provider, 'action' => $action]);
    }

    public function actionClientModal() {
        $client = new Client('search');
        $client->attributes = $_GET;

        $provider = $client->search();

        $this->renderPartial('client_modal', ['dataProvider' => $provider]);
    }

    public function actionClientQuery() {
        $client = new Client('search');
        $client->attributes = $_GET;

        $criteria = new CDbCriteria(['with' => 'group', 'scopes' => ['belongsToUser' => [Yii::app()->user->model]]]);
        $provider = $client->search($criteria);
        $provider->pagination->pageVar = 'page';

        $products = array_map(
                function ($client) {
            /** @var Client $client */
            return [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'group' => $client->group->name,
            ];
        }, $provider->data
        );

        echo json_encode(
                ['items' => $products, 'more' => $provider->pagination->currentPage + 1 < $provider->pagination->pageCount]
        );
    }

    public function actionSellerQuery() {
        $user = Yii::app()->user->model;
        $sellers = new User('search');
        $sellers->attributes = $_GET;

        $criteria = new CDbCriteria;
        $criteria->addCondition("shop_id = " . $user->shop_id . " and role = 'seller'");

        $provider = $sellers->search($criteria);
        $provider->pagination->pageVar = 'page';

        $products = array_map(
                function ($sellers) {
            /** @var User $user */
            return [
                'id' => $sellers->id,
                'full_name' => $sellers->full_name,
            ];
        }, $provider->data
        );

        echo json_encode(
                ['items' => $products, 'more' => $provider->pagination->currentPage + 1 < $provider->pagination->pageCount]
        );
    }

}
