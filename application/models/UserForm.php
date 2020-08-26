<?php
namespace app\models;

use app\models\job\EmailJob;
use Yii;
use app\models\dao\Role;
use app\models\dao\User;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\models\dao\Channel;

class UserForm extends Model
{
    protected $_user;
    protected $_channelIds;
    protected $_roleIds;
    protected $_permissions;

    public $password;
    public $confirmPassword;

    public $usingAutoGeneratedPassword;
    public $sendEmailNotification;

    const SCENARIO_CREATE = "CREATE";
    const SCENARIO_UPDATE = "UPDATE";

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['User',], 'required'],
            [['User', 'channelIds', 'roleIds'], 'safe'],

            [['password', 'confirmPassword'], 'trim', 'on' => self::SCENARIO_CREATE],
            [['password'], 'string', 'min' => 8, 'max' => 128, 'on' => self::SCENARIO_CREATE],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password'],
            // ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'on' => self::SCENARIO_CREATE],

            [['sendEmailNotification'], 'integer', 'on' => self::SCENARIO_CREATE],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['User', 'channelIds', 'roleIds', 'password', 'confirmPassword', 'sendEmailNotification'];
        $scenarios[self::SCENARIO_UPDATE] = ['User', 'channelIds', 'roleIds', 'usingAutoGeneratedPassword', 'password', 'confirmPassword'];
        return $scenarios;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'usingAutoGeneratedPassword' => "Using auto-generated password",
            'sendEmailNotification' => 'Send email to inform account created',
            'password' => 'Password',
            'confirmPassword' => 'Confirm Password',
        ];
    }

    /**
     * @param $user
     */
    public function setUser($user){
        if($user instanceof User){
            $this->_user = $user;
        } else if($this->_user instanceof User && is_array($user)){
            $this->_user->setAttributes($user);
        }
    }

    /**
     * @return mixed
     */
    public function getUser(){
        return $this->_user;
    }

    /**
     * Check user before save
     * @return bool
     */
    protected function beforeSaveUser(){
        if(!$this->user->validate()){
            return false;
        }
        if($this->user->isNewRecord){
            if($this->user->is_admin === User::IS_ADMIN){
                if(!$this->user->created_by || ($someone = User::findOne($this->user->created_by)) === null || !$someone->isAdmin()){
                    $this->user->addError('is_admin', 'This field can be set by an administrator');
                    return false;
                }
            }
        }
        else {
            if(array_key_exists('is_admin', $this->user->getDirtyAttributes())){
                if(!$this->user->updated_by || ($someone = User::findOne($this->user->updated_by)) === null || !$someone->isAdmin()){
                    $this->user->addError('is_admin', 'This field can be set by an administrator');
                    return false;
                }
            }
            if($this->password){
                $this->user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
                $this->user->password_last_changed_at = new Expression('NOW()');
            }
        }
        return true;
    }

    /**
     * Save user information
     * @return bool
     */
    public function saveUser()
    {
        if(!$this->beforeSaveUser() || !$this->user->save()){
            $this->addErrors($this->user->getErrors());
            return false;
        }
        return true;
    }

    /**
     * @return array|null
     */
    public function getChannelIds(){
        if($this->_channelIds === null){
            if($this->user->isNewRecord){
                $this->_channelIds = [];
            } else {
                $this->_channelIds = ArrayHelper::getColumn($this->user->channels, 'id');
            }
        }
        return $this->_channelIds;
    }

    /**
     * @param $ids
     */
    public function setChannelIds($ids)
    {
        if($ids == ''){
            $ids = [];
        }
        if(!is_array($ids)){
            $ids = [$ids];
        }
        $this->_channelIds = $ids;
    }

    /**
     * @return bool
     */
    public function saveChannelIds()
    {
        $keep = [];
        foreach($this->user->channels as $model){
            if(in_array($model->id, $this->_channelIds)){
                $keep[] = $model->id;
            } else {
                $this->user->unlink('channels', $model, true);
            }
        }

        $query = Channel::find()->where(['id' => $this->_channelIds]);
        if($keep){
            $query->andWhere(['not in', 'id', $keep]);
        }
        foreach($query->all() as $model){
            $this->user->link('channels', $model, ['updated_at' => new Expression('NOW()')]);
        }
        return true;
    }

    /**
     * @return array|null
     */
    public function getRoleIds(){
        if($this->_roleIds === null){
            if($this->user->isNewRecord){
                $this->_roleIds = [];
            } else {
                $this->_roleIds = ArrayHelper::getColumn($this->user->roles, 'id');
            }
        }
        return $this->_roleIds;
    }

    /**
     * @param $ids
     */
    public function setRoleIds($ids)
    {
        if($ids == ''){
            $ids = [];
        }
        if(!is_array($ids)){
            $ids = [$ids];
        }
        $this->_roleIds = $ids;
    }

    /**
     * @return bool
     */
    public function saveRoleIds()
    {
        $keep = [];
        foreach($this->user->roles as $model){
            if(in_array($model->id, $this->_roleIds)){
                $keep[] = $model->id;
            } else {
                $this->user->unlink('roles', $model, true);
            }
        }

        $query = Role::find()->where(['id' => $this->_roleIds]);
        if($keep){
            $query->andWhere(['not in', 'id', $keep]);
        }
        foreach($query->all() as $model){
            $this->user->link('roles', $model, ['updated_at' => new Expression('NOW()')]);
        }
        return true;
    }

    /**
     * @return bool
     */
    public function save(){
        //auto-generate password
        if($this->usingAutoGeneratedPassword){
            $this->password = $this->confirmPassword = Yii::$app->security->generateRandomString(8);
        }

        //verify
        if(!$this->validate()){
            return false;
        }

        //save user
        if(!$this->saveUser()){
            return false;
        }

        //save channels
        if(!$this->saveChannelIds()){
            return false;
        }

        //save roles
        if(!$this->saveRoleIds()){
            return false;
        }

        //send an email notification for account created
        if($this->scenario === self::SCENARIO_CREATE && $this->user->isActive() && $this->sendEmailNotification){
            Yii::$app->queue->push(new EmailJob([
                'view' => 'users/account_successfully_created',
                'viewParams' => [
                    'user' => $this->user,
                    'loginType' => 'Email',
                    'loginId' => $this->user->email,
                    'password' => $this->password,
                ],
                'to' => $this->user->email,
                'subject' => ' Password has been updated.'
            ]));
        }

        return true;
    }
}