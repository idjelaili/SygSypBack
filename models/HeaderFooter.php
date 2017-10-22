<?php

class HeaderFooter {

	private $idHeaderFooter;
	private $header;
	private $footer;
	private $mailConsultant;

	function __construct() {
	}
	
	function creerHeaderFooter($headerFooter) {
	
		$this->header = $headerFooter ['header'];
		$this->footer = $headerFooter ['footer'];
		$this->mailConsultant = $headerFooter ['mailConsultant'];
	}
	
	function setHeader($header) {
		$this->header = $header;
	}
	
	function setfooter($footer) {
		$this->footer = $footer;
	}
	
	function setmailConsultant($mailConsultant) {
		$this->mailConsultant = $mailConsultant;
	}
	
	
	function getidHeaderFooter() {
		return $this->idHeaderFooter;
	}
	
	function getHeader() {
		return $this->header;
	}
	
	function getFooter() {
		return $this->footer;
	}
	
	function getMailConsultant() {
		return $this->mailConsultant;
	}
	
}