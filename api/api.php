<?php
header("Content-Type:application/json");
require_once('../conn/db_config.php');

// first check we have the all important team id
//if(request('teamid')) {
    if(request('functionName')) {
        $status_message = 'API Success';
        if(request('functionName') == "getPlayers") {
            response(200, $status_message, getPlayers(request('teamid')));
        }
        if(request('functionName') == "getAllStats") {
            response(200, $status_message, getAllStats(request('playerid'), request('season'), request('comp'), request('opponent'), request('runs'), request('wickets'), request('groupby'), request('orderby'), request('limit')));
        }
        if(request('functionName') == "getBattingPartnerships") {
            response(200, $status_message, getBattingPartnerships(request('partnership'), request('wicket'), request('orderby'), request('limit')));
        }
        if(request('functionName') == "getMatchPerformances") {
            response(200, $status_message, getMatchPerformances(request('playerid'), request('match'), request('innings'), request('orderby')));
        }
    } else {
        $status_message = 'Function Name missing';
        response(400, $status_message, NULL);
    }
//} else {
    //$status_message = 'Team ID missing';
    //response(400, $status_message, NULL);
//}



function getPlayers($teamid) {
    $data = array();
    $sql = '
    SELECT u.firstname, u.lastname, u.email, u.retired
    FROM user u
    INNER JOIN player_team pt 
    ON u.userid = pt.userid
    WHERE pt.teamid = ' . $teamid . '
    AND u.deleted = 0
    ORDER BY u.firstname, u.lastname ASC';

    $result = db_query($sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data['lastname'][] = $row['lastname'];
        $data['firstname'][] = $row['firstname'];
    }
    return $data;

}

function getAllStats($playerid, $season, $comp, $opponent, $runs, $wickets, $groupby, $orderby, $limit) {
    $data = array();
    $sql = '
    SELECT p.playerid, concat(firstname," ",lastname) as fullname, count(distinct m.matchid) as matches, ifnull(sum(runs),"-") as runs, count(bat.battingid) as innings,
    ifnull(sum(runs)/sum(dismissal),"-") as bataverage, ifnull(sum(ballsfaced),"-") as ballsfaced, ifnull(sum(boundaries),"0") as fours, ifnull(sum(sixes), "0") as sixes,
    max(runs) as hs, ifnull(sum(wickets),"-") as wickets, ifnull(sum(maidens),"-") as maidens, ifnull(sum(runsconceded),"-") as runsconceded, ifnull(sum(wides),"-") as wides,
    ifnull(sum(noballs),"-") as noballs, ifnull(sum(catches),"0") as catches, ifnull(sum(stumpings),"0") as stumpings, sum(byes) as byes,
    ifnull(sum(runsconceded)/sum(wickets),"-") as bowlaverage, ifnull(sum(deliveries)/sum(wickets),"-") as strikerate, ifnull(sum(deliveries),"-") as deliveries,
    ifnull(sum(runsconceded)/sum(deliveries/6),"-") as rpo, count(bat.battingid)-sum(dismissal) as notouts, sum(fifty) as fifty, sum(hundred) as ton,
    ifnull(sum(fivewickets),"-") as fivewickets, ifnull(sum(tenwickets),"-") as tenwickets, ifnull(sum(runs)/sum(ballsfaced)*100,"-") as srate,
    sum(catches)+sum(stumpings) as dismissals, c.competition, c.compid,opponent, season, bat.did, c.mtid, p.matchinnings
    from performance p
    inner join user u
    on p.playerid = u.userid
    inner join matches m
    on p.matchid = m.matchid
    left outer join batting bat
    on p.battingid = bat.battingid
    left outer join bowling bowl
    on p.bowlingid = bowl.bowlingid
    left outer join fielding f
    on p.fieldingid = f.fieldingid
    left outer join competition c
    on m.compid = c.compid
    where m.teamid = 1';

    if($playerid <> "") $sql .= ' AND p.playerid = "' . $playerid . '"';
    if($season <> "") $sql .= ' AND m.season = "' . $season . '"';
    if($comp <> "") $sql .= ' AND ' . $comp;
    if($opponent <> "") $sql .= ' AND opponent = "' . $opponent . '"';
    if($runs <> "") $sql .= ' AND ' . $runs;
    if($wickets <> "") $sql .= ' AND ' . $wickets;

    if($groupby <> "") $sql .= ' GROUP BY ' . $groupby;

    if($orderby <> "") {
        $orderby = str_replace("_", " ", $orderby);
        $sql .= ' ORDER BY ' . $orderby;
    }

    if($limit <> "") $sql .= ' LIMIT ' . $limit;

    $result = db_query($sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data['cricketwizardid'][] = $row['playerid'];
        $data['matches'][] = $row['matches'];
        $data['runs'][] = $row['runs'];
        $data['wickets'][] = $row['wickets'];
        $data['season'][] = $row['season'];
        $data['sixes'][] = $row['sixes'];
        $data['opponent'][] = $row['opponent'];
        $data['catches'][] = $row['catches'];
        $data['dismissal'][] = $row['did'];
        $data['fullname'][] = $row['fullname'];
        $data['innings'][] = $row['innings'];
        $data['bataverage'][] = $row['bataverage'];
        $data['hs'][] = $row['hs'];
        $data['ton'][] = $row['ton'];
        $data['fifty'][] = $row['fifty'];
        $data['fours'][] = $row['fours'];
        $data['stumpings'][] = $row['stumpings'];
        $data['notouts'][] = $row['notouts'];
        $data['deliveries'][] = $row['deliveries'];
        $data['maidens'][] = $row['maidens'];
        $data['rpo'][] = $row['rpo'];
        $data['bowlaverage'][] = $row['bowlaverage'];
        $data['strikerate'][] = $row['strikerate'];
        $data['fivewickets'][] = $row['fivewickets'];
        $data['tenwickets'][] = $row['tenwickets'];
        $data['runsconceded'][] = $row['runsconceded'];
        $data['dismissals'][] = $row['dismissals'];
    }
    return $data;
}

function getBattingPartnerships($partnership, $wicket, $orderby, $limit) {
    $data = array();
    $sql = '
    SELECT pa.id as partnershipid, pa.wicket, concat(p1.firstname," ", p1.lastname) as player1, concat(p2.firstname," ", p2.lastname) as player2, pa.partnership, m.opponent, m.venue, m.season, m.date, m.matchid, m.teamid, p1.userid as p1id, p2.userid as p2id, inningsid 
    FROM partnerships pa
    INNER JOIN matches m 
    ON pa.matchid = m.matchid
    LEFT OUTER JOIN user p1
    ON pa.batsmanid1 = p1.userid
    LEFT OUTER JOIN user p2
    ON pa.batsmanid2 = p2.userid
    WHERE m.teamid=1
    AND partnership<>10101';

    if($partnership <> "") $sql .= ' AND partnership >= ' . $partnership;
    if($wicket <> "") $sql .= ' AND pa.wicket = ' . $wicket;
    if($orderby <> "") {
        $orderby = str_replace("_", " ", $orderby);
        $sql .= ' ORDER BY ' . $orderby;
    }

    if($limit <> "") $sql .= ' LIMIT ' . $limit;

    $result = db_query($sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data['player1'][] = $row['player1'];
        $data['player2'][] = $row['player2'];
        $data['partnership'][] = $row['partnership'];
        $data['opponent'][] = $row['opponent'];
        $data['venue'][] = $row['venue'];
        $data['season'][] = $row['season'];
        $data['wicket'][] = $row['wicket'];
        $data['matchid'][] = $row['matchid'];
        $data['inningsid'][] = $row['inningsid'];
        $data['p1id'][] = $row['p1id'];
        $data['p2id'][] = $row['p2id'];
    }
    return $data;
}

function getMatchPerformances($playerid, $match, $innings, $orderby) {
    $data = array();
    $sql = '
    select concat(firstname," ", lastname) as player, p.playerid,bat.runs, ifnull(bat.ballsfaced,"-") as ballsfaced, ifnull(bat.boundaries,"-") as boundaries, ifnull(bat.sixes,"-") as sixes, bowl.deliveries, ifnull(bowl.maidens,"-") as maidens, bowl.runsconceded, bowl.wickets, ifnull(bowl.wides,"-") as wides, ifnull(bowl.noballs,"-") as noballs, f.stumpings, f.byes, f.catches, dis.dismissal, p.matchinnings,bowl.overs, bat.battingid, bowl.bowlingid, f.fieldingid, dis.did,p.pid,firstname,p.matchinnings, p.matchid, bat.batorder, bowl.bowlorder, bat.batorder
    FROM performance p
    INNER JOIN user player
    ON p.playerid = player.userid
    LEFT OUTER JOIN batting bat
    ON p.battingid = bat.battingid
    LEFT OUTER JOIN bowling bowl
    ON p.bowlingid = bowl.bowlingid
    LEFT OUTER JOIN fielding f
    ON p.fieldingid = f.fieldingid
    LEFT OUTER JOIN dismissal dis
    ON bat.did = dis.did
    WHERE p.playerid = ' . $playerid;

    if($match <> "") $sql .= ' AND p.matchid = ' . $match;
    if($innings <> "") $sql .= ' AND p.matchinnings = ' . $innings;

    if($orderby <> "") {
        $orderby = str_replace("_", " ", $orderby);
        $sql .= ' ORDER BY ' . $orderby;
    }
    $result = db_query($sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data['did'][] = $row['did'];
    }
    return $data;
}

function response($status, $status_message, $data) {
    header("HTTP/1.1 " . $status);
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    $json_response = json_encode($response);
    echo $json_response;
}
//response(getPlayers());
//$test = getPlayers();
//print_r($test);