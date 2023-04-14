<?php

namespace App\AccountModul\Controller;

use App\AccountModul\Model\AccountManager;
use App\AccountModul\Model\PersonDetailTable;
use App\AccountModul\Model\UserTable;
use App\ArticleModul\Model\ArticleManager;
use App\BaseModul\System\Controller\Controller;
use App\BaseModul\System\Controller\RouterController;
use Couchbase\User;
use Micho\Exception\UserException;
use Micho\Exception\ValidationException;
use Micho\Form\Form;
use Micho\Form\Input;
use Micho\Form\Validator;
use Micho\Files\File;

class AccountController extends Controller
{

    /**
     ** Spracovanie zobrazenia možnosti výberu aplikácii
     * @return void
     * @Action
     */
    public function application()
    {
        $this->userVerification();

        $article = new ArticleManager();
        RouterController::$subPageControllerArray['title'] = 'Application'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Application selection options'; // pridanie doplnujúceho description k hlavnému

        $articlesUrl = ['amazon-ads', 'amazon-monthly-sales'];

        $this->data['applications'] = $article->loadArticles($articlesUrl,array(ArticleManager::TITLE, ArticleManager::DESCRIPTION,ArticleManager::LINK));

        $this->data['subview'] = 'application';
        $this->view = 'template-in';
    }


    /**
     ** Nastavenie a zmena osobných údajov
     * @return void
     * @Action
     **/
    public function personalInformation()
    {
        $this->userVerification();

        $userId = UserTable::$user[UserTable::USER_ID];
        $email = UserTable::$user[UserTable::EMAIL];

        $personDetailManager = new PersonDetailTable();
        $personDetailData = $personDetailManager->getPersonalData($userId);

        $form = new Form('personal-info', array_merge(PersonDetailTable::$personDetailDataKeys, array(UserTable::EMAIL)));
        $form->addInput('personID', PersonDetailTable::PERSON_DETAIL_ID, Input::TYPE_HIDDEN);
        $form->editControlParameters(Form::EMAIL, array(Input::VALUE => $email, Input::DISABLED => true));

        $form->addSubmit('Save','save-button','', 'btn btn-primary btn-block font-weight-bolder');

        if($form->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $form->getData();
                $form->validate($formData);
                $accountManager = new AccountManager();
                $accountManager->savePersonalInformation($formData);

                $this->addMessage('Personal data has been updated.', self::MSG_SUCCESS);
                $this->redirect();
            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }
        elseif ($personDetailData)
            $form->setValuesControls($personDetailData);


        $this->data['form'] = $form->createForm();

        RouterController::$subPageControllerArray['title'] = 'Personal information'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Personal data of the logged-in user, personal information'; // pridanie doplnujúceho description k hlavnému

        $this->data['subview'] = 'personal-information';
        $this->view = 'template-in';
    }

    /**
     ** Nastavenie, zmena hesla
     * @return void
     * @Action
     */
    public function passwordChange()
    {
        $form = new Form('password-change', array(Form::PASSWORD));
        $form->addInput('Old password','old_password',Input::TYPE_PASSWORD);
        $form->addInput('Password again', 'password_again', Input::TYPE_PASSWORD, '', '','','',true,Validator::PATTERN_PASSWORD);
        $form->addSubmit('Change','password-change-button','', 'btn btn-primary btn-block font-weight-bolder');

        if($form->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $form->getData();

                $form->validate($formData, Form::TYPE_CHANGE_PASSWORD);

                $userManager = new UserTable();
                $userManager->changePassword($formData[UserTable::PASSWORD], UserTable::$user[UserTable::USER_ID]);

                $this->addMessage('The password has been changed.', self::MSG_SUCCESS);
                $this->redirect('');

            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }

        $this->data['form'] = $form->createForm();

        RouterController::$subPageControllerArray['title'] = 'Password change'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Saving a new password, Changing Password'; // pridanie doplnujúceho description k hlavnému

        $this->data['subview'] = 'password-change';
        $this->view = 'template-in';
    }

    /**
     ** Odhlásenie uživateľa
     * @Action
     */
    public function logOut()
    {
        $userManager = new UserTable();
        $userManager->logOut();
        $this->addMessage('You have been successfully logged out.', self::MSG_SUCCESS);
        $this->redirect(' ');
    }

    /**
     ** Spracuje prihlásenie uživateľa
     * @return void
     * @Action
     */
    public function login()
    {
        if(UserTable::$user)
            $this->redirect('account/personal-information');
        
        $form = new Form('login', array(Form::EMAIL, Form::PASSWORD));
        $form->addSubmit('Login','login-button','', 'btn btn-primary btn-block font-weight-bolder');

        if($form->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $form->getData();
                $form->validate($formData);

                $accountManager = new AccountManager();
                $accountManager->login($formData[UserTable::EMAIL], $formData[UserTable::PASSWORD]);

                $this->addMessage('You have been successfully logged in.', self::MSG_SUCCESS);
                $this->redirect('account/personal-information');

            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
            catch (UserException $error)
            {
                $this->addMessage($error->getMessage(), self::MSG_ERROR);
                $this->redirect(); // musim presmerovat kvoli vygenerovnaiu noveho CSRF
            }
        }

        $this->data['form'] = $form->createForm();

        RouterController::$subPageControllerArray['title'] = 'Login'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Login to the user account'; // pridanie doplnujúceho description k hlavnému

        $this->data['title'] =  RouterController::$subPageControllerArray['title'];
        $this->data['subview'] = 'login';
        $this->view = 'template-out';
    }


    /**
     ** Spracuje vytvorenie nového konta uživateľa
     * @return void
     * @Action
     */
    public function newAccount()
    {
        if(UserTable::$user)
            $this->redirect('account/personal-information');

        $form = new Form('registration', array(Form::EMAIL, Form::PASSWORD));
        $form->addInput('Password again', 'password_again', Input::TYPE_PASSWORD, '', '','','',true,Validator::PATTERN_PASSWORD);
        $form->addSubmit('Create an account','create-button','', 'btn btn-primary btn-block font-weight-bolder');

        if($form->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $form->getData();
                $form->validate($formData, Form::TYPE_REGISTRATION);

                $accountManager = new AccountManager();
                $accountManager->createNewAccount($formData);
                $this->addMessage('You have been successfully registered. ', self::MSG_SUCCESS);
                $this->addMessage('Registration data has been sent to your email. ', self::MSG_SUCCESS);

                $accountManager = new AccountManager();

                $accountManager->login($formData[UserTable::EMAIL], $formData[UserTable::PASSWORD]);
                $this->addMessage('You have been successfully logged in.', self::MSG_SUCCESS);

                $this->redirect('account/personal-information');
            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
            catch (UserException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessage(), self::MSG_ERROR);
            }
        }

        $this->data['form'] = $form->createForm();

        RouterController::$subPageControllerArray['title'] = 'New account'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Creating a new user account'; // pridanie doplnujúceho description k hlavnému

        $this->data['title'] =  RouterController::$subPageControllerArray['title'];
        $this->data['subview'] = 'new-account';
        $this->view = 'template-out';
    }

    /**
     ** Spracuje zabudnutie hesla uživateľa
     * @return void
     * @Action
     */
    public function forgotPassword()
    {
        if(UserTable::$user)
            $this->redirect('account/personal-information');

        $form = new Form('forgot-password', array(Form::EMAIL));
        $form->addSubmit('Send password','send-password','', 'btn btn-primary btn-block font-weight-bolder');

        if($form->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $form->getData();
                $form->validate($formData, Form::TYPE_FORGOTTEN_PASSWORD);

                $accountManager = new AccountManager();
                $accountManager->createNewPassword($formData[UserTable::EMAIL]);

                $this->addMessage('The new password has been sent to your email: ' . $formData[Form::EMAIL] . '.', self::MSG_SUCCESS);
                $this->redirect('account/login');
            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }
        $this->data['form'] = $form->createForm();

        RouterController::$subPageControllerArray['title'] = 'Forgot password'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Sending a new password'; // pridanie doplnujúceho description k hlavnému

        $this->data['title'] =  RouterController::$subPageControllerArray['title'];
        $this->data['subview'] = 'forgot-password';
        $this->view = 'template-out';
    }
}

/*
 * Autor: MiCHo
 */