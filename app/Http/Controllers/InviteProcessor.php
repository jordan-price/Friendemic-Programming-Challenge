<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InviteProcessor extends Controller
{
    /**
     * Process the invite file.
     *
     * @param  file  $inviteFile
     * @return Response
     */
	 public function processInvite(Request $request){
		$this->validate($request, [
			'inviteFile'	=> 'required',
		]);
		
		// Assign today's date
		$datetime	= 1520208000;
		
		// Read invite file data
		$inviteFile	= fopen($request->file('inviteFile')->getPathName(), "r");
		$output		= array();
		$isFirst	= true;
		while(($line=fgetcsv($inviteFile)) !== FALSE)
		{
			// Check isFirst flag to skip header
			if($isFirst)
			{
				$isFirst = false;
				continue;
			}
			
			// Read data per column
			$reference = count($output);
			foreach($line AS $key=>$value)
			{
				switch($key){
					case 0: // TYPE & APPLY DEFAULT VALUES
						$output[$reference]['_duplicate']=false;
						$output[$reference]['_rsvp']=true;
						$output[$reference]['_phone']=false;
						$output[$reference]['_properTime']=true;
						$output[$reference]['_type']=$value;
						break;
					case 2: // TIME
						// subtract the current date (march 5th 2018) by 7 days, then check if this rsvp qualfies in the time bracket
						if(($datetime - 604800) > strtotime("{$output[$reference][1]} {$value}")){
							// if it's less than the 7 day subtraction, then it does not qualify so we satasfy the flags
							$output[$reference]['_properTime']=false;
							$output[$reference]['_rsvp']=false;
						}
						break;
					case 5: // EMAIL
						if(array_search($value, array_column($output, 5)) != FALSE && !empty($value))
						{
							// record exist, mark record duplicate flag & rsvp to false
							$output[$reference]['_duplicate']=true;
							$output[$reference]['_rsvp']=false;
						}
						break;
					case 6: // PHONE
						if(!empty($value)) // set phone flag to true, if available.
							$output[$reference]['_phone']=true;
						if(array_search($value, array_column($output, 6)) != FALSE && !empty($value))
						{
							// record exist, mark record duplicate flag & rsvp to false
							$output[$reference]['_duplicate']=true;
							$output[$reference]['_rsvp']=false;
						}
						break;
				}
				$output[$reference][$key]=trim($value);
			}
		}
		fclose($inviteFile);
		
		// Set current date to be read
		$datetime = date("M dS, Y  g:i A", $datetime);
		
		// Build output HTML
		$HTML = <<<TABLE_PREPEND
			<table id='output' class="table" style='display:none;'>
				<thead>
					<tr>
						<th colspan=10>Current Date & Time: {$datetime}</th>
					</tr>
					<tr>
						<th>Type</th>
						<th>Date</th>
						<th>Time</th>
						<th>Customer #</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th class="bg-primary p-2 text-white">Invite Type</th>
						<th class="bg-primary p-2 text-white">Method</th>
						<th class="bg-primary p-2 text-white">Invite Sent</th>
					</tr>
				</thead>
				<tbody>
TABLE_PREPEND;
		foreach($output as $reference => $data)
		{
			$_inviteSent	= ($data['_rsvp']) ? "<td class='bg-primary p-2 text-white'><i class='fas fa-check-circle'></i> Successfully Sent</td>" : "";
			$_methodSent	= ($data['_phone']) ? "<i class='fas fa-mobile-alt'></i> SMS" : "<i class='fas fa-envelope'></i> E-mail";
			// provide helpful information as to why an invite was not sent
			if(!$data['_rsvp'])
			{
				$_msg = "";
				if($data['_duplicate'])
					$_msg = "This is a duplicate record by email or phone number.";
				if(!$data['_properTime'])
					$_msg = (!empty($_msg)) ? "{$_msg}<br />It&#39;s not within the last several days." : "It&#39;s not within the last several days.";
				$_inviteSent .= "<td class='bg-danger p-2 text-white' data-toggle='popover' data-trigger='hover' title='RSVP Not Sent' data-content='{$_msg}'><i class='fas fa-times-circle'></i> Not Sent</td>";
			}
			
			$HTML .= <<<OUTPUT_HTML
					<tr>
						<td>{$data[0]}</td>
						<td>{$data[1]}</td>
						<td>{$data[2]}</td>
						<td>{$data[3]}</td>
						<td>{$data[4]}</td>
						<td>{$data[5]}</td>
						<td>{$data[6]}</td>
						<td class="bg-primary p-2 text-white">{$data['_type']}</td>
						<td class="bg-primary p-2 text-white">{$_methodSent}</td>
						{$_inviteSent}
					</tr>
OUTPUT_HTML;
		}
		$HTML .= <<<TABLE_APPEND
				</tbody>
			</table>
TABLE_APPEND;
		// Return results
		return response()->json(['output'=>$output, 'html'=>$HTML]);
	 }
}
