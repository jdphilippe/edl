<?php

class DateUtils {

	private $mp_date_comment = array();
	private $mp_date_avent = array();
	private $mp_cancelled_date = array();

	public function __construct() {
		$currentYear = date('Y' );
		for ($year = 2016; $year <= $currentYear; $year++) {
			$this->getAdventDates ($year);

			$this->mp_date_comment[ $this->dimanche_rameaux($year) ]     = 'Rameaux';
			$this->mp_date_comment[ $this->lundi_saint($year) ]          = 'Lundi Saint';
			$this->mp_date_comment[ $this->mardi_saint($year) ]          = 'Mardi Saint';
			$this->mp_date_comment[ $this->mercredi_saint($year) ]       = 'Mercredi Saint';
			$this->mp_date_comment[ $this->jeudi_saint($year) ]          = 'Jeudi Saint';
			$this->mp_date_comment[ $this->vendredi_saint($year) ]       = 'Vendredi Saint';
			$this->mp_date_comment[ $this->samedi_saint($year) ]         = 'Samedi Saint';
			$this->mp_date_comment[ $this->dimanche_paques($year) ]      = 'Pâques';
			$this->mp_date_comment[ $this->jeudi_ascension($year) ]      = 'Ascension';
			$this->mp_date_comment[ $this->dimanche_pentecote($year) ]   = 'Pentecôte';
			$this->mp_date_comment[ $this->dimanche_reformation($year) ] = 'Réformation';
			$this->mp_date_comment[ $this->premier_dimanche_avent() ]           = '1<sup>er</sup> Dim Avent';
			$this->mp_date_comment[ $this->deuxieme_dimanche_avent() ]          = '2<sup>ème</sup> Dim Avent';
			$this->mp_date_comment[ $this->troisieme_dimanche_avent() ]         = '3<sup>ème</sup> Dim Avent';
			$this->mp_date_comment[ $this->quatrieme_dimanche_avent() ]         = '4<sup>ème</sup> Dim Avent';
			$this->mp_date_comment[ "24/12/$year" ]                             = 'Veillée de Noël';
			$this->mp_date_comment[ "25/12/$year" ]                             = 'Noël';

			if ( $year === 2020 ) {
				$this->mp_date_comment['12/01/2020'] = '150 ans, Ep. 1';  // Batir un temple
				$this->mp_date_comment['08/03/2020'] = '150 ans, Ep. 4';  // Predication de l'archeveque
				$this->mp_date_comment['12/04/2020'] = '150 ans, Ep. 12'; // Temple détruit et ressuscité
				$this->mp_date_comment['24/05/2020'] = '150 ans, Ep. 16'; // Concert - culte (Oratoire du Louvre)
				$this->mp_date_comment['21/06/2020'] = '150 ans, Ep. 18'; // Culte - fete de la musique
				$this->mp_date_comment['25/10/2020'] = '150 ans, Ep. 25'; // Culte de la Reformation
				$this->mp_date_comment['25/12/2020'] = '150 ans, Ep. 30'; // Culte de Noel

				$this->mp_cancelled_date['17/03/2020'] = 'Evénement reporté';
				$this->mp_cancelled_date['21/03/2020'] = 'Evénement annulé';
			}
		}
	}

	public function getComment( $timestamp ) {
		$date = date( 'd/m/Y', $timestamp);
		if ( ! array_key_exists( $date, $this->mp_date_comment ) )
			return '';

		$year       = date('Y', $timestamp);
		$day_number = date('N', $timestamp);
		if ( $date === "24/12/$year" && $day_number === '7') {
			/*
			 * Si nous sommes le 24/12 et que ce jour est un dimanche, il y aura deux cultes.
			 * Seul celui du soir correspond a la veillee
			 */
			$time = (int) date( 'H', $timestamp );
			if ( $time <= 18 )
				return '';
		}

		return $this->mp_date_comment[ $date ];
	}

	public function getCancelledDateComment( $timestamp ) : string {
		$date = date( 'd/m/Y', $timestamp);
		if ( ! array_key_exists( $date, $this->mp_cancelled_date ) )
			return '';

		return $this->mp_cancelled_date[ $date ];
	}

	private function getAdventDates ( $annee): void {
		$date = mktime(0,0,0,11,25,$annee);
		$sundays = 0;
		while ( $sundays < 4 ) {
			$date = date( 'Y/m/d', $date);
			$date = strtotime( "$date + 1 days" );
			$day_number = date('N', $date );
			if ($day_number === '7') {
				$this->mp_date_avent[ $sundays ] = date( 'd/m/Y',$date);
				$sundays++;
			}
		}
	}

	private function premier_dimanche_avent() {
		return $this->mp_date_avent[0];
	}

	private function deuxieme_dimanche_avent() {
		return $this->mp_date_avent[1];
	}

	private function troisieme_dimanche_avent() {
		return $this->mp_date_avent[2];
	}

	private function quatrieme_dimanche_avent() {
		return $this->mp_date_avent[3];
	}

	private function dimanche_reformation( $annee ) {
		$date_reformation = strtotime("last Sunday of October $annee" );

		return date('d/m/Y', $date_reformation );
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

		$annee = $annee ?? date( 'Y' );

		$G = $annee % 19;
		$C = floor($annee / 100);
		$C_4 = floor($C / 4);
		$E = floor((8 * $C + 13) / 25);
		$H = (19 * $G + $C - $C_4 - $E + 15) % 30;

		if ( $H === 29 ) {
			$H = 28;
		} else if ( $H === 28 && $G > 10 ) {
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
		$Jour = $mois === 3 ? $R : $R - 31;

		return mktime(0,0,0, $mois,$Jour + $Jourj,$annee);
	}

	private function dimanche_paques_internal( $annee ) {
		$datePaques = $this->paques(0, $annee);

		return date('Y/m/d', $datePaques);
	}

	private function dimanche_paques( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( $dimanche_paques) );
	}

	private function samedi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 1 days" ) );
	}

	private function vendredi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 2 days" ) );
	}

	private function jeudi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 3 days" ) );
	}

	private function mercredi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 4 days" ) );
	}

	private function mardi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 5 days" ) );
	}

	private function lundi_saint( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 6 days" ) );
	}

	private function dimanche_rameaux( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques - 7 days" ) );
	}

	private function jeudi_ascension( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques + 39 days" ) );
	}

	private function dimanche_pentecote( $annee ) {
		$dimanche_paques = $this->dimanche_paques_internal( $annee );

		return date('d/m/Y', strtotime( "$dimanche_paques + 49 days" ) );
	}
}

/**
 * Get posts in range date by a specific custom field(post meta)
 *
 * @param string $start start date
 * @param string $end end date
 * @param array $cats categories
 * @param string $ctype
 *
 * @return array  of posts
 */
function get_posts_between( $start, $end, $cats = [], $ctype = 'post' ) {
	$args = array (
		'post_type'      => $ctype,
		'orderby'        => 'post_date',
		'post_status'    => 'publish',
		'cat'            => $cats, // [ 778, 779, 780, 781 ],
		'posts_per_page' => - 1,
		'date_query' => array (
			'before'    => array (
				'year'  => date( 'Y', $end ),
				'month' => date( 'm', $end ),
				'day'   => date( 'd', $end ),
			),
			'after'     => array (
				'year'  => date( 'Y', $start ),
				'month' => date( 'm', $start ),
				'day'   => date( 'd', $start ),
			),
			'inclusive' => true
		)
	);

	$query = new WP_Query($args );
	return $query->get_posts();
}
