<?php
namespace show\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;

/**
 * JCoins create listener.
 *
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.show
 */
class JCoinsEntryEventListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		$objects = $eventObj->getObjects();
		
		switch ($eventObj->getActionName()) {
			case 'triggerPublication':
				foreach ($eventObj->getObjects() as $object) {
					if ($object->userID) {
						UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.statement.show.entry', $object->getDecoratedObject());
					}
				}
				break;
				
				// 'enable' calls triggerPublication
				
			case 'disable':
				foreach ($eventObj->getObjects() as $object) {
					if (!$object->isDeleted && $object->userID) {
						UserJCoinsStatementHandler::getInstance()->revoke('com.uz.jcoins.statement.show.entry', $object->getDecoratedObject());
					}
				}
				break;
				
			case 'trash':
				foreach ($eventObj->getObjects() as $object) {
					if (!$object->isDisabled && $object->userID) {
						UserJCoinsStatementHandler::getInstance()->revoke('com.uz.jcoins.statement.show.entry', $object->getDecoratedObject());
					}
				}
				break;
				
			case 'restore':
				foreach ($eventObj->getObjects() as $object) {
					if (!$object->isDisabled && $object->userID) {
						UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.statement.show.entry', $object->getDecoratedObject());
					}
				}
				break;
		}
	}
}