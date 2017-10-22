<?php

interface EntrepriseDao {

    public function createEntreprise( $code_gener, $nomEntreprise, $mailConsultant, $adresse, $adresseComplement, $codePostal, $ville, $telephone );

    public function setConsultantEntreprise($mailConsultant, $nomEntreprise);

    public function existeEntreprise($nomEntreprise);

    public function getEntreprises($mailConsultant);

    public function getAllEntrepriseAttribue($mailConsultant);
}
