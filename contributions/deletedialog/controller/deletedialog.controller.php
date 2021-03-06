<?php
/**
 * This controller catches delete commands
 * 
 * @author Gerd Riesselmann
 * @ingroup DeleteDialog
 */
class DeleteDialogController extends ControllerBase {
	/**
	 * returns array of routes
	 * 
	 * @return array 
	 */
	public function get_routes() {
		$ret = array(
			new CommandsRoute('https://process_commands/{model:s}/{id:ui>}/delete', $this, 'deletedialog_cmd_handler', new NoCacheCacheManager()),
			new ParameterizedRoute('https://deletedialog/approve/{_model_:s}/{id:ui>}', $this, 'deletedialog_approve', new NoCacheCacheManager()),
			
			new CommandsRoute('https://process_commands/{model:s}/{id:ui>}/status/DELETED', $this, 'deletedialog_status_cmd_handler', new NoCacheCacheManager()),
			new ParameterizedRoute('https://deletedialog/approve/status/{_model_:s}/{id:ui>}', $this, 'deletedialog_approve_status', new NoCacheCacheManager()),
		);
		return $ret;
	}
	
	/**
	 * Handle the command, that is: Display an approval dialog
	 *
	 * @param PageData $page_data
	 * @param string $model
	 * @param string $id
	 */
	public function action_deletedialog_status_cmd_handler(PageData $page_data, $model, $id) {
		$page_data->in_history = false;
		$dao = $this->get_instance($model, $id);
		if ($dao === false) {
			return CONTROLLER_NOT_FOUND;
		}
		Url::create(ActionMapper::get_url('deletedialog_approve_status', $dao))->redirect();
	}

	/**
	 * Handle link approval
	 *
	 * @param PageData $page_data
	 * @param string $_model_
	 * @param string $id
	 */
	public function action_deletedialog_approve_status(PageData $page_data, $_model_, $id) {
		$page_data->in_history = false;
		
		$dao = $this->get_instance($_model_, $id);
		if ($dao === false) {
			return CONTROLLER_NOT_FOUND;
		}
		
		$cmd = CommandsFactory::create_command($dao, 'status', 'DELETED');
		if (!$cmd->can_execute(false)) {
			return CONTROLLER_ACCESS_DENIED;
		}
		
		$formhandler = $this->create_formhandler();
		if ($page_data->has_post_data()) {
			$err = $formhandler->validate();
			if ($err->is_ok()) {
				if ($page_data->get_post()->get_item('cancel', false) !== false) {
					$err = new Message(tr('Deletion has been canceled by user', 'deletedialog'));
				}
				else {
					$err->merge($cmd->execute());
				}
			}
			$instance_string = $this->get_instance_name($dao);
			$formhandler->finish($err, tr('The '.$instance_string.' has been deleted', 'deletedialog'));
		}
		else {
			$this->render_view_status($page_data, $formhandler, $dao);	
		}
	}

    /**
     * Extract instance name from DAO instance
     *
     * Strips of plural s, like 'users' => 'user'
     *
     * @param DataObjectBase $dao
     * @return string
     */
    protected function get_instance_name($dao) {
        if ($dao instanceof ISelfDescribingType) {
            // Should be the default in about 99,9% of all cases
            return $dao->get_type_name_singular();
        } else if ($dao instanceof IDataObject) {
            // This is just in case someone implements a IDataObject without ISelfDescribingType in the Future
            // You never know...
            return $dao->get_table_name();
        } else if ($dao instanceof ISelfDescribing) {
            // Or some weird mind implements a model without IDataObject at all
            // Most unlikely, but it will be handled :)
            return $dao->get_title();
        } else {
            // Everyone needs a default, for the very very unlikely situations
            return 'instance';
        }
    }
	
	
	/**
	 * HAndle the command, that is: Display an approval dialog
	 *
	 * @param PageData $page_data
	 * @param string $model
	 * @param string $id
	 */
	public function action_deletedialog_cmd_handler(PageData $page_data, $model, $id) {
		$page_data->in_history = false;
		$dao = $this->get_instance($model, $id);
		if ($dao === false) {
			return CONTROLLER_NOT_FOUND;
		}
		
		Url::create(ActionMapper::get_url('deletedialog_approve', $dao))->redirect();
	}

	/**
	 * Handle link approval
	 *
	 * @param PageData $page_data
	 * @param string $_model_
	 * @param string $id
	 */
	public function action_deletedialog_approve(PageData $page_data, $_model_, $id) {
		$page_data->in_history = false;
		
		$dao = $this->get_instance($_model_, $id);
		if ($dao === false) {
			return CONTROLLER_NOT_FOUND;
		}
		
		$cmd = CommandsFactory::create_command($dao, 'delete', false);
		if (!$cmd->can_execute(false)) {
			return CONTROLLER_ACCESS_DENIED;
		}
		
		$formhandler = $this->create_formhandler();
		if ($page_data->has_post_data()) {
			$err = $formhandler->validate();
			if ($err->is_ok()) {
				if ($page_data->get_post()->get_item('cancel', false) !== false) {
					$err = new Message(tr('Deletion has been canceled by user', 'deletedialog'));
				}
				else {
					$err->merge($cmd->execute());
				}
			}
			$formhandler->finish($err, tr('The instance has been deleted', 'deletedialog'));
		}
		else {
			$this->render_view($page_data, $formhandler, $dao);	
		}
	}
	
	/**
	 * Returns instance passed as coded string
	 *
	 * @param string $model
	 * @param string $id
	 * @return IDataObject
	 */
	protected function get_instance($model, $id) {
		$ret = false;
		$dao = DB::create($model);
		if (!empty($dao)) {
			$arr_id = explode(GYRO_COMMAND_ID_SEP, $id);
			foreach ($dao->get_table_keys() as $key => $field) {
				$dao->$key = array_shift($arr_id);
			}
			
			if ($dao->find(IDataObject::AUTOFETCH) == 1) {
				// Not zero or many items
				$ret = $dao;					
			}
		}
		return $ret;
	}
	
	/**
	 * Create a formhandler
	 *
	 * @return FormHandler
	 */
	protected function create_formhandler() {
		Load::tools('formhandler');
		return new FormHandler('dlgdeleteapprove');		
	}
	
	/**
	 * Create an render approval view
	 *
	 * @param FormHandler $formhandler
	 * @param IDataObject $instance
	 */
	protected function render_view(PageData $page_data, FormHandler $formhandler, $instance) {
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'deletedialog/approve', $page_data);
		$formhandler->prepare_view($view);
		$view->assign('instance', $instance); 
		$view->render();		
	}
	
	/**
	 * Create an render approval view
	 *
	 * @param FormHandler $formhandler
	 * @param IDataObject $instance
	 */
	protected function render_view_status(PageData $page_data, FormHandler $formhandler, $instance) {
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'deletedialog/approve_status', $page_data);
		$formhandler->prepare_view($view);
		$view->assign('instance', $instance); 
		$view->render();		
	}
	
}