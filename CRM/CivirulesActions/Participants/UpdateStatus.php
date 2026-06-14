class CRM_CivirulesActions_Participant_UpdateStatus extends CRM_Civirules_Action {
  // SQL:
  // INSERT INTO civirule_action (name, label, class_name, is_active) 
  // VALUES("participant_update_status", "Update Participant Status", "CRM_CivirulesActions_Participant_UpdateStatus", 1)

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    CRM_Core_Error::debug_log_message("Update Participant Status: " . print_r($triggerData, true));
  }

  /**
   * Method to return the url for additional form processing for action
   * and return false if none is needed
   *
   * @param int $ruleActionId
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }

}

