<?php

/**
 * Description of code
 *
 * @author robertopereznygaard
 */
class CodeController extends CodazoController
{
    protected   $_callingFrom = 'www';

	public function __construct($params = array())
	{
		parent::__construct();

        if (isset($params['callingFrom']))
        {
            $this->_callingFrom = $params['callingFrom'];
            switch ($this->_callingFrom)
            {
                case 'api':
                    $this->_typeOfView = 'json';
            }
        }
	}

    protected function _loadForm()
    {
        $this->_app->register(new Silex\Provider\FormServiceProvider);
        $form = $this->_app['form.factory']->createBuilder('form')
            ->add('code', 'textarea', array('label' => ' ', 'attr' => array('style' => 'height: 300px', 'class' => 'span12')))
            ->add('langLabel', 'text', array('label' => 'Language', 'required' => false, 'attr' => array('placeholder' => 'Auto')))
            ->add('line', 'text', array('label' => 'First line', 'required' => false, 'attr' => array('placeholder' => '1')))
            ->add('lang', 'hidden', array('required' => false ))
            ->getForm();

        return $form;
    }

    protected function _saveCodeModel($data)
    {
        if (!$this->_checkVitalParams($data) || !is_array($data))
        {
            $this->_app->error(function (\Exception $e, $code) {
                return new Response('Wrong parameters received.');
            });
            return false;
        }

        $values = array(
            'code' => $data['code'],
            'created_at' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'options' => serialize( array(
                'language' => $data['lang'],
                'first_line' => $data['line']
            ) )

        );

        $codeModel = new CodeModel();
        $uniqueId = $codeModel->saveCode($values);

        return $uniqueId;
    }

	public function index()
	{
		$form = $this->_loadForm();

		$viewParams = array(
			'form' => $form->createView(),
			'currentSection' => 'paste'
		);

		return $this->_render('paste.twig', $viewParams);
	}

    public function saveCode()
    {
        $request = $this->_app['request'];
        $form = $this->_loadForm();
        $form->bindRequest($request);
        if ($form->isValid())
        {
            $data = $form->getData();
            $uniqueId = $this->_saveCodeModel($data);

            return $this->_app->redirect('/' . $uniqueId);
        }
        //If not valid...
        $viewParams = array(
            'form' => $form->createView(),
            'currentSection' => 'paste'
        );
        return $this->_render('paste.twig', $viewParams);
    }

	public function viewCode($id)
	{
		$codeModel = new CodeModel();
        $reversibleUID = new ReversibleUniqueId();
		$code = $codeModel->get(array('id' => $reversibleUID->decode($id)));
		$options = unserialize( $code['options'] );

		$viewParams = array(
			'code' => $code['code'],
			'language'	=> $options['language'],
			'first_line'	=> $options['first_line'],
			'uid' => $id
		);

        if ($this->_callingFrom == 'www')
        {
            $viewParams['currentSection'] = 'view';
        }

		return $this->_render('view.twig', $viewParams);
	}

	public function viewRaw($id)
	{
		$codeModel = new CodeModel();

		return $this->_render('view_raw.twig', array('code' => $codeModel->get(array('unique_id' => $id))));
	}

}