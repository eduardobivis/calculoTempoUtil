//Tempo entre duas Datas e Horários contando apenas as horas de trabalho
function calculoTempoUtil(DateTime $inicio, DateTime $termino, $horarioInicial, $horarioTermino) { 

	########
	# Dias #
	########
	$diasUteis = [1, 2, 3, 4, 5];
	$feriados = [
		'*-01-01',
		'*-12-25', 
	]; 

	$interval = new DateInterval('P1D');
	$periodos = new DatePeriod($inicio, $interval, $termino);

	$totalDias = -1;
	foreach ($periodos as $periodo) {
		if (!in_array($periodo->format('N'), $diasUteis)) continue;
		if (in_array($periodo->format('*-m-d'), $feriados)) continue;
		$totalDias++;
	}

	//Caso o início e o Termino sejam no mesmo dia
	if($totalDias == 0) {
		$dateInterval = $inicio->diff($termino);
		$retorno = "";
		if($dateInterval->y > 0) $retorno .= "{$dateInterval->y} anos, "; 
		if($dateInterval->m > 0) $retorno .= "{$dateInterval->m} meses, "; 
		if($dateInterval->d > 0) $retorno .= "{$dateInterval->d} dias, "; 
		if($dateInterval->h > 0) $retorno .= "{$dateInterval->h} horas, ";
		$retorno .= "{$dateInterval->m} minutos"; 
		return $retorno;
	}

	//Calcula as Horas Úteis do Primeiro Dia caso Dia útil
	if (in_array($inicio->format('N'), $diasUteis)) {
		$terminoInicio = clone $inicio;
		$terminoInicio->modify($horarioTermino);
		$horasInicio = $inicio->diff($terminoInicio);
	}

	//Calcula as Horas Úteis do Último Dia caso Dia Útil
	if (in_array($termino->format('N'), $diasUteis)) {
		$terminoTermino = clone $termino;
		$terminoTermino->modify($horarioInicial);
		$horasTermino = $termino->diff($terminoTermino);
	}

	$totalHoras = 0;
	$totalMinutos = 0;
	$totalSegundos = 0;

	if(isset($horasInicio)) {
		$totalDias += $horasInicio->d;
		$totalHoras += $horasInicio->h;
		$totalMinutos += $horasInicio->i;
		$totalSegundos += $horasInicio->s;
	}
	if(isset($horasTermino)) {
		$totalDias += $horasTermino->d;
		$totalHoras += $horasTermino->h;
		$totalMinutos += $horasTermino->i;
		$totalSegundos += $horasTermino->s;
	}	

	//Tratamento para formato 24h
	if($totalSegundos > 60) {
		$totalMinutos += floor( $totalSegundos / 60 );
		$totalSegundos = $totalSegundos % 60;
	}

	if($totalMinutos > 60) {
		$totalHoras += floor( $totalMinutos / 60 );
		$totalMinutos = $totalMinutos % 60;
	}

	if($totalHoras > 8) {
		$totalDias += floor( $totalHoras / 8 );
		$totalHoras = $totalHoras % 8;
	}

	$dateInterval = new DateInterval("P{$totalDias}DT{$totalHoras}H{$totalMinutos}M");

	$retorno = "";
	if($dateInterval->y > 0) $retorno .= "{$dateInterval->y} anos, "; 
	if($dateInterval->m > 0) $retorno .= "{$dateInterval->m} meses, "; 
	if($dateInterval->d > 0) $retorno .= "{$dateInterval->d} dias, "; 
	if($dateInterval->h > 0) $retorno .= "{$dateInterval->h} horas, ";
	$retorno .= "{$dateInterval->m} minutos"; 
	return $retorno;
}
