<?php
function RechercheScoreTeam($equipe1,$equipe2){
	/**
	 * recherche les équipes existe dans l'api
	 */
	$id_team1 ="";
	$id_team2 ="";
	$uri = 'http://api.football-data.org//v2/competitions/FL1/teams';
	$reqPrefs['http']['method'] = 'GET';
	$reqPrefs['http']['header'] = 'X-Auth-Token: d50dc13d2e724192b99f02b872a0ea2e';
	$stream_context = stream_context_create($reqPrefs);
	$response = file_get_contents($uri, false, $stream_context);
	$teams = json_decode($response,true);
	$name_team=array();
	foreach ($teams['teams'] as $v) {
		$name_team[$v['id']]= mb_strtolower($v['name']."(".$v['shortName'].")",'UTF-8');
	 }

	/**
	 * Function pour rechercher une equipe dans un tableau multi-dimensionnel
	 */ 
	foreach ($name_team as $key => $y) {
		if (preg_match('['.$equipe1.']',$y)) {
			$id_team1 = $key;
		}
		if (preg_match('['.$equipe2.']',$y)) {
			$id_team2 = $key;
		}
	}
	if (($id_team1 == "")||($id_team2 == "")) {
		return "l'une des équipe n'existe pas... vérifier l'orthographe !";
	}else{

	/**
	 *  recherche les équipes à domicile, les scores et les matches qui n'ont pas encore eu lieu dans l'api
	 */
	$uri = 'http://api.football-data.org/v2/teams/'.$id_team1.'/matches';
	$reqPrefs['http']['method'] = 'GET';
	$reqPrefs['http']['header'] = 'X-Auth-Token: d50dc13d2e724192b99f02b872a0ea2e';
	$stream_context = stream_context_create($reqPrefs);
	$response = file_get_contents($uri, false, $stream_context);
	$matches = json_decode($response,true);

	$match = 0;
	$message_return="";
	foreach ($matches['matches'] as $z) {
			if ((preg_match('['.$id_team1.']',$z['homeTeam']['id']) && preg_match('['.$id_team2.']',$z['awayTeam']['id']) || (preg_match('['.$id_team1.']',$z['awayTeam']['id']) && preg_match('['.$id_team2.']',$z['homeTeam']['id'])))) {
				$score_home =$z['score']['fullTime']['homeTeam'];
				$score_away =$z['score']['fullTime']['awayTeam'];
				if ($score_away==null && $score_home==null) {
					if ($match == 0) {
					$message_return.= "le match aller est prévu le :\n".substr($z['utcDate'], 0, 10)."\n";
						$match = 1;
					}else if ($match == 1) {
						$message_return.= "le match retour est prévu le :\n".substr($z['utcDate'], 0, 10)."\n" ;
					}				
				}else{
					if ($match == 0) {
					$message_return.= "Score match aller :\n".$z['homeTeam']['name'].' '.$score_home."\n".$z['awayTeam']['name'].' '.$score_away." \n";
						$match = 1;
					}else if ($match == 1) {
										$message_return.= "Score match retour:\n".$z['homeTeam']['name'].' '.$score_home."\n".$z['awayTeam']['name'].' '.$score_away." \n";
					}
				}
			}
		}
		return $message_return;
	}
}

/**
 *  Fonction pour récuprer les classement des équipes de ligue 1
 */
function ClassementEquipe(){
	$id_team1 ="";
	$id_team2 ="";
	$uri = 'http://api.football-data.org//v2/competitions/FL1//standings';
	$reqPrefs['http']['method'] = 'GET';
	$reqPrefs['http']['header'] = 'X-Auth-Token: d50dc13d2e724192b99f02b872a0ea2e';
	$stream_context = stream_context_create($reqPrefs);
	$response = file_get_contents($uri, false, $stream_context);
	$teams = json_decode($response,true);
	$message_return="";
	foreach ($teams['standings'][0]['table'] as $v) {
		$message_return.= $v['position']." ".$v['team']['name']." (".$v['points']." pts) \n";
	 }
	 return $message_return;
}