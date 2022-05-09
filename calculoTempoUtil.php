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
	$periods = new DatePeriod($inicio, $interval, $termino);

	$dias = 0;
	foreach ($periods as $period) {
		if (!in_array($period->format('N'), $diasUteis)) continue;
		if (in_array($period->format('*-m-d'), $feriados)) continue;
		$dias++;
	}

	//Horas
	$terminoInicio = clone $inicio;
	$terminoInicio->modify($horarioTermino);
	$horasInicio = $inicio->diff($terminoInicio);

	$terminoTermino = clone $termino;
	$terminoTermino->modify($horarioInicial);
	$horasTermino = $termino->diff($terminoTermino);

	$totalDias = ( $dias -1 ) + $horasInicio->d + $horasTermino->d;
	$totalHoras = $horasInicio->h + $horasTermino->h;
	$totalMinutos = $horasInicio->i + $horasTermino->i;

	return "{$totalDias} dias, {$totalHoras} Horas e {$totalMinutos} Minutos";
}
