<?php
require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Model/ParticipationModel.php';

$Config = new Config();
$db = $Config->getPDO();
$participationModel = new ParticipationModel($db);

// Back-office page: render list_participations.php template.
$participations = []; // TODO: Fetch real participations if needed
include __DIR__ . '/list_participations.php';

