<?php

class DateUtils {

	private $mp_date_comment = array();

	function __construct() {
		$currentYear = date("Y");
		for ($year = 2016; $year <= $currentYear; $year++) {
			$this->mp_date_comment[ $this->mardi_saint($year) ]        = 'Mardi Saint';
			$this->mp_date_comment[ $this->mercredi_saint($year) ]     = 'Mercredi Saint';
			$this->mp_date_comment[ $this->jeudi_saint($year) ]        = 'Jeudi Saint';
			$this->mp_date_comment[ $this->vendredi_saint($year) ]     = 'Vendredi Saint';
			$this->mp_date_comment[ $this->samedi_saint($year) ]       = 'Samedi Saint';
			$this->mp_date_comment[ $this->dimanche_paques($year) ]    = 'Pâques';
			$this->mp_date_comment[ $this->jeudi_ascension($year) ]    = 'Ascension';
			$this->mp_date_comment[ $this->dimanche_pentecote($year) ] = 'Pentecôte';
			$this->mp_date_comment[ "24/12/$year" ]                    = 'Veillée de Noël';
			$this->mp_date_comment[ "25/12/$year" ]                    = 'Noël';
		}
	}

	public function getComment( $timestamp ) {
		$date = date( "d/m/Y", $timestamp);
		if ( ! array_key_exists( $date, $this->mp_date_comment ) )
			return "";

		$year       = date("Y", $timestamp);
		$day_number = date('N', $timestamp);
		if ( $date == "24/12/$year" && $day_number == 7) {
			/*
			 * Si nous sommes le 24/12 et que ce jour est un dimanche, il y aura deux cultes.
			 * Seul celui du soir correspond a la veillee
			 */
			$time = intval( date( "H", $timestamp ) );
			if ( $time <= 18 )
				return "";
		}

		return $this->mp_date_comment[ $date ];
	}

	private function paques( $Jourj = 0, $annee = NULL ) {
		/* *** Algorithme de Oudin, calcul de Pâque postérieure à 1583 ***
		 * Transcription pour le langage PHP par david96 le 23/03/2010
		 * *** Source : www.concepteursite.com/paques.php ***
		 * Attributs de la fonction :
		 * $Jourj : représente le jour de la semaine
		 * (0=dimanche, 1=lundi...)
		 * par défaut c'est le dimanche
		 * $annee : représente l'année recherchée pour la date de Pâques
		 * par défaut c'est l'année en cours.
		 * */

		$annee = ( $annee == NULL ) ? date("Y") : $annee;

		$G = $annee % 19;
		$C = floor($annee / 100);
		$C_4 = floor($C / 4);
		$E = floor((8 * $C + 13) / 25);
		$H = (19 * $G + $C - $C_4 - $E + 15) % 30;

		if ( $H == 29 ) {
			$H = 28;
		} else if ( $H == 28 && $G > 10 ) {
			$H = 27;
		}

		$K = floor($H / 28);
		$P = floor(29 / ( $H + 1 ));
		$Q = floor((21 - $G) / 11 );
		$I = ($K * $P * $Q - 1) * $K + $H;
		$B = floor($annee / 4) + $annee;
		$J1 = $B + $I + 2 + $C_4 - $C;
		$J2 = $J1 % 7; //jour de pâques (0=dimanche, 1=lundi....)
		$R = 28 + $I - $J2; // résultat final :)
		$mois = $R > 30 ? 4 : 3; // mois (1 = janvier, ... 3 = mars...)
		$Jour = $mois == 3 ? $R : $R - 31;

		return mktime(0,0,0, $mois,$Jour + $Jourj, $annee);
	}


	private function dimanche_paques_internal( $annee ) {
		$datePaques = $this->paques(0, $annee);

		return date("Y/m/d", $datePaques);
	}

	private function dimanche_paques( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date("d/m/Y", strtotime( $dimanche_paques) );
	}

	private function samedi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques - 1 days" ) );
	}

	private function vendredi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques - 2 days" ) );
	}

	private function jeudi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques - 3 days" ) );
	}

	private function mercredi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques - 4 days" ) );
	}

	private function mardi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques - 5 days" ) );
	}

	private function jeudi_ascension( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques + 39 days" ) );
	}

	private function dimanche_pentecote( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date( "d/m/Y", strtotime( "$dimanche_paques + 49 days" ) );
	}
}