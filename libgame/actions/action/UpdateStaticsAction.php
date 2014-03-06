<?php
include_once GAMELIB.'/model/UserFriendManager.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
class UpdateStaticsAction extends GameActionBase{
	protected function _exec()
	{
		$payLog = LogFactory::getLogger(array(
			'prefix' => LogFactory::LOG_MODULE_PLATFORM,
			'log_dir' => APP_ROOT.'/log/analytic/', // 文件所在的目录
			'archive' => ILogger::ARCHIVE_YEAR_MONTH, // 文件存档的方式
			'log_level' => 1
		));
		
		$gameuid = $this->getParam("gameuid",'string');
		$action_id = $this->getParam("actionId",'string');
		
		$payLog->writeInfo($gameuid."   actionID:  ".$action_id);
		return  true;
	}
}
?>