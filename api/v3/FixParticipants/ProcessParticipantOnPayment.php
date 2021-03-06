<?php
use CRM_Fixparticipants_ExtensionUtil as E;

/**
 * FixParticipants.process_participant_on_payment API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_fix_participants_process_participant_on_payment(&$spec) {
  $spec['magicword']['api.required'] = 1;
}

/**
 * FixParticipants.process_participant_on_payment API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_fix_participants_process_participant_on_payment($params) {
	if (array_key_exists('magicword', $params) && $params['magicword'] == 'einodmilvado'
		&& array_key_exists('events', $params)
	) {
	$events = explode(',', $params['events']);

	$result = process_participants($events);
	if ($result['is_error']) {
	    return civicrm_api3_create_error($result['is_error']);
	}
	return civicrm_api3_create_success(implode("\r\r", $result['messages']));
  }
    return civicrm_api3_create_error('Error while processing participant statuses');
}

/**
 * - Registered (1):
 * - Cancelled (4): "Cancelled"
 * 	-> Registered (1) if completed > 0
 * - Expired (12): "Expired"
 * 	-> Registered (1) if completed > 0
 * - Pending (6): "Pending from incomplete transaction"
 * 	-> Expired (12) if completed == 0
 * 	-> Registered (1) if completed > 0
 */
function process_participants($events) {
	$returnMessages = array("<br/>");

	$pending = civicrm_api3('Participant', 'get', [
	  'sequential' => 1,
	  'status_id' => ["Pending from incomplete transaction", "Cancelled", "Expired"],
	  "event_id" => $events,
	  'options' => ['sort' => "id desc", 'limit' => 5000,],
	  'api.ParticipantPayment.get' => [],
	]);

	echo "Found: " . count($pending['values']);
	foreach ($pending['values'] as $participant) {
		$id = $participant['participant_id'];
		$status = $participant['participant_status'];
		$message = "<br/>Participant: '{$participant['display_name']}' ($id) is '$status' for event '{$participant['event_title']}'<br/>";
		$contribution_ids = $participant['api.ParticipantPayment.get']['values'];
		$completed = 0;
		foreach ($contribution_ids as $cid) {
			$contribution = civicrm_api3('Contribution', 'get', [
			  'sequential' => 1,
			  "return" => ["contribution_status_id"],
			  'options' => ['sort' => "id asc"],
			  'id' => $cid['contribution_id'],
			]);
			// $message .= json_encode($contribution);
			if ($contribution['is_error'] == 0 && $contribution['count'] == 1) {
				$contribution_status = $contribution['values'][0]['contribution_status'];
				if ($contribution_status == 'Completed') {
					$completed += 1;
				}
				$message .= "   Contribution: {$cid['contribution_id']} ($contribution_status)<br/>";
			}
		}
		$message .= "=== completed: $completed ";
		$status_id = '';
		switch ($status) {
		case "Registered":
			break;
		case "Cancelled":
			if ($completed > 0) {
				$message .=  "=> 'Registered(1)'";
				$status_id = "Registered";
			}
			break;
		case "Expired":
			if ($completed > 0) {
				$message .=  "=> 'Registered(1)'";
				$status_id = "Registered";
			}
			break;
		case "Pending (incomplete transaction)":
			if ($completed == 0) {
				$message .=  "=> 'Expired(12)'";
				$status_id = "Expired";
			} else if ($completed > 0) {
				$message .=  "=> 'Registered(1)'";
				$status_id = "Registered";
			}
			break;
		default:
			break;
		}
		if ($status_id != '') {
			$result = civicrm_api3('Participant', 'create', [
				'id' => $id,
				'status_id' => $status_id,
			]);
			$message .= json_encode($result);
		}
		$returnMessages[] .=  $message . "<br/>";
	}

	return array('is_error' => 0, 'messages' => $returnMessages);
}

