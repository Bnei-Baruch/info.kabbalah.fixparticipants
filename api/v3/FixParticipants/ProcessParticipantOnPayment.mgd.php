<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:FixParticipants.process_participant_on_payment',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Call FixParticipants.process_participant_on_payment API',
      'description' => 'Call FixParticipants.process_participant_on_payment API',
      'run_frequency' => 'Always',
      'api_entity' => 'FixParticipants',
      'api_action' => 'process_participant_on_payment',
      'parameters' => '',
    ),
  ),
);
