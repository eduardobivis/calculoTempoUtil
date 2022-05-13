public class SLA {

	################################
	# Tempo entre Início e Término #
	################################
	public function calculoTempoBruto(DateTime $inicio, DateTime $termino) {
		$dateInterval = $inicio->diff($termino);
		return $this->formataResultado($dateInterval);
	}

	#####################################################################
	# Tempo entre Início e Término contando apenas as horas de trabalho #
	#####################################################################
	public function calculoTempoUtil(
		DateTime $inicio, 
		DateTime $termino, 
		$horarioInicial, 
		$horarioTermino
	) { 
		
		//Dias
		$diasUteis = [1, 2, 3, 4, 5];
		$feriados = [
			'*-01-01',
			'*-12-25', 
		]; 

		$interval = new DateInterval('P1D');
		$periods = new DatePeriod($inicio, $interval, $termino);

		$totalDias = -1;
		foreach ($periods as $period) {
			if (!in_array($period->format('N'), $diasUteis)) continue;
			if (in_array($period->format('*-m-d'), $feriados)) continue;
			$totalDias++;
		}
		//Caso tenha iniciado e finalizado no mesmo dia
		if($totalDias <= 0) {
			return $this->calculoTempoBruto($inicio, $termino);
		}

		$intervalos = [];

		//Calcula as Horas Úteis do Primeiro Dia caso Dia útil
		if (in_array($inicio->format('N'), $diasUteis)) {
			$terminoInicio = clone $inicio;
			$terminoInicio->modify($horarioTermino);
			$horasInicio = $inicio->diff($terminoInicio);
			$intervalos[] = $horasInicio;
		}
		
		//Calcula as Horas Úteis do Último Dia caso Dia Útil
		if (in_array($termino->format('N'), $diasUteis)) {
			$terminoTermino = clone $termino;
			$terminoTermino->modify($horarioInicial);
			$horasTermino = $terminoTermino->diff($termino);
			$intervalos[] = $horasTermino;
		}

		return $this->adicionaIntervalos( $intervalos, $totalDias ) //Adiciona e Trata Resultado
	}

	######################################################################
	# Adiciona objetos do tipo DateInterval, convertendo para padrão 24h #
	######################################################################
	public function adicionaIntervalos($intervalos, $totalDias = 0, $tratada = true) {

		$totalHoras = 0;
		$totalMinutos = 0;
		$totalSegundos = 0;
		$totalMeses = 0;
		$totalAnos = 0;

		foreach($intervalos as $intervalo) {

			$totalDias += $intervalo->d;
			$totalHoras += $intervalo->h;
			$totalMinutos += $intervalo->i;
			$totalSegundos += $intervalo->s;
		}

		if($totalSegundos > 60) {
			$totalMinutos += (int) floor( $totalSegundos / 60 );
			$totalSegundos = $totalSegundos % 60;
		}

		if($totalMinutos > 60) {
			$totalHoras += (int) floor( $totalMinutos / 60 );
			$totalMinutos = $totalMinutos % 60;
		}

		if($totalHoras > 8) {
			$totalDias += (int) floor( $totalHoras / 8 );
			$totalHoras = $totalHoras % 8;
		}

		if($totalDias > 30) {
			$totalMeses += (int) floor( $totalDias / 30 );
			$totalDias = $totalDias % 30;
		}

		if($totalMeses > 12) {
			$totalAnos += (int) floor( $totalMeses / 12 );
			$totalMeses = $totalMeses % 12;
		}

		$dateInterval = new DateInterval("P{$totalAnos}Y{$totalMeses}M{$totalDias}DT{$totalHoras}H{$totalMinutos}M{$totalSegundos}S");

		//Formata o resultado ou retorna o Intervalo
		return $this->formataResultado($dateInterval);

	}	

	#####################		
	# Formata Resultado #
        #####################			
	public function formataResultado(DateInterval $dateInterval) {
		$retorno = "";
		if($dateInterval->y > 0) $retorno .= "{$dateInterval->y} anos, "; 
		if($dateInterval->m > 0) $retorno .= "{$dateInterval->m} meses, "; 
		if($dateInterval->d > 0) $retorno .= "{$dateInterval->d} dias, "; 
		if($dateInterval->h > 0) $retorno .= "{$dateInterval->h} horas, ";
		if($dateInterval->i > 0) $retorno .= "{$dateInterval->i} minutos, ";
		$retorno .= "{$dateInterval->s} segundos"; 
		return $retorno;
	}
}
