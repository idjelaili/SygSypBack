<?php
// Etablire la connexion avec la base de donnée

class FonctionUtils {

	/**
	 * Fonction qui permet de ce rediriger vers une autre page.
	 * $lien : Le lien vers la page destinataire.
	 */
	public static function Redirection($Lien) {
		header ( "Location: $Lien" );
		exit ();
	}

	/**
	 * Fonction qui permet de sécuriser la base de donnée des injections sql.
	 * $Valeur : la variable à sécuriser .
	 * */
	public static function escape_errors ($Valeur){

		// Retourne la configuration actuelle de la fonction "magic_quotes_gpc".
		$magic_quotes = get_magic_quotes_gpc();
		// Tester si la function "mysql_real_escape_string" existe ou pas.
		$version_recente = function_exists("mysql_real_escape_string");
		if($version_recente){
			if($magic_quotes){
				// supprimer tous les antislashs.
				$Valeur = stripcslashes($Valeur);
				// enlever les caractères spéciaux.
				$Valeur = mysql_real_escape_string($Valeur);
			}else {
				$Valeur = addslashes($Valeur);
			}
		}else{
			if(!$magic_quotes){
				$Valeur = addslashes($Valeur);
			}
		}
		$Valeur = trim($Valeur);
		return $Valeur;
	}

	/**
	 * Fonction qui permet d'affiche le message d'erreur associer au numéro d'erreur
	 * MOP : Mot de passe oublié.
	 * $num_erreur : la variable à sécuriser .
	 * */
	public static function affiche_erreur ($num_erreur){

		if(!empty($num_erreur)){
			if($num_erreur == 1){
				$message = "Vous n'êtes pas encore inscrit";
			}else if($num_erreur == 2){
				$message = "Votre adresse mail n'est pas valide .";
			}else if($num_erreur == 3){
				$message = "Votre Mail est incorrecte !";
			}else if($num_erreur == 4){
				$message =  "Tous les champs doivent être complétés.";
			}else if($num_erreur == 5){
				$message = "Le compte a bien été modifié.";
			}else if($num_erreur == 6){
				$message = "Adresse mail déja utilisée";
			}else if($num_erreur == 7){
				$message = "Votre mail est incorrect !";
			}else if($num_erreur == 8){
				$message ="Votre prénom est trop grand ! Max 45 caractères";
			}else if($num_erreur == 9){
				$message = "Votre nom est trop grand ! Max 45 caractères";
			}else if($num_erreur == 10){
				$message = "Inscription terminée";
			}else if($num_erreur == 11){
				$message ="Votre Mot de passe est incorrecte !";
			}else if($num_erreur == 12){
				$message ="Votre mot de passe doivent contenir au minimum 4 caractères.";
			}else if($num_erreur == 14){
				$message =  "Votre nom ou prénom n'est pas valide.";
			}else if($num_erreur == 15){
				$message = "Votre numéro de téléphone n'est pas valide.";
			}else if($num_erreur == 16){
				$message = "votre date de naissance n'est pas valide.";
			}else if($num_erreur == 17){
				$message = "vous êtes maintenant rattacher à l'entreprise.";
			}else if($num_erreur == 18){
				$message = "Le captcha est incorrect !";
			}else if($num_erreur == 19){
				$message = "Entreprise inexistante.";
			}else if($num_erreur == 20){
				$message = "Votre code postale est incorrect";
			}else if ($num_erreur == 23){
				$message = "Information bien modifiées.";
			}else if ($num_erreur == 24){
				$message ="Veuillez consulter votre boîte email :)";
			}else if($num_erreur == 100){
				$message = "Votre mot de passe a bien été modifié.";
			}else if($num_erreur == 25){
				$message = "Ce champ ne doit contenir que des lettres";
			}else if($num_erreur == 26){
				$message = "Mail de rénitialisation envoyé";
			}else if($num_erreur == 27){
				$message = "Code entreprise incorrect";
			}else if($num_erreur == 28){
				$message = "Entreprise crée et code envoyé";
			}else if($num_erreur == 29){
				$message = "Une erreur est survenu lors de l'envoie du mail";
			}else if($num_erreur == 30){
				$message = "Header et Footer enregistrés ";
			}else if($num_erreur == 31){
				$message = "Erreur (configuration SMTP) merci de contacter un administrateur (voir mention legale dans Accueil)";
			}else if($num_erreur == 32){
				$message = "Merci de se connecter avec un compte consultant)";
			}else if($num_erreur == 33){
				$message = "La taille de l'image est trop grande";
			}else if($num_erreur == 34){
				$message = "Un compte existe déjà avec cette adresse mail !";
			}
		}
		return $message;
	}

	/**
	 * Fonction qui permet de vérifier si l'object qu'on a upload est une image.
	 * $avatar_tmp : L'objet qu'on veut upload.
	 * $avatar : Le nom de l'image
	 * $idUser : l'identifiant de l'utilisateur
	 * $url : le chemin vers dossier image .
	 * */
	public static function upload_avatar ($avatar_tmp, $avatar, $idUser, $url)
	{
		// initialisation de la variable d'erreur.
		$erreur = "";
		if(!empty($avatar_tmp)){
			// séparation du nom et l'extension de l'image.
			$image = explode('.',$avatar);
			// récupérer l'extension de l'image.
			$image_ext = end($image);
			// faire un test sur l'extension de l'image
			if(in_array(strtolower($image_ext),array('png','gif','jpeg','jpg')) == false){
				$erreur = "Veuillez saisir une image";
			}
		}
		if(empty($erreur)){
			// test sur l'existence de l'image
			if(file_exists($avatar_tmp)){
				$image_size = getimagesize($avatar_tmp);
				// tester si l'objet est une image
				if($image_size['mime'] == 'image/jpeg'){
					$image_src = imagecreatefromjpeg($avatar_tmp);
				}else if($image_size['mime'] == 'image/png'){
					$image_src = imagecreatefrompng($avatar_tmp);
				}else if($image_size['mime'] == 'image/gif'){
					$image_src = imagecreatefromgif($avatar_tmp);
				}else if($image_size['mime'] == 'image/jpg'){
					$image_src = imagecreatefromjpg($avatar_tmp);
				}else{
					$erreur = "Votre image n'est pas validée !!";
					$image_src= false;
				}
				if($image_src != false){
					// définition de la taille obligatoire de l'image
					$width_image_std = 200;
					if($image_size[0] <= $width_image_std){
						$image_finale = $image_src;
					}else {
						// nouvelle largeur de l'image
						$new_width[0] = $width_image_std;
						// calculer de la nouvelle hauteur de l'image
						$new_height[1] = 200;
						// Cr�e une nouvelle image
						$image_finale = imagecreatetruecolor($new_width[0], $new_height[1]);
						// faire une copie de l'image source dans une nouvelle image avec des dimensions réduite
						imagecopyresampled($image_finale, $image_src, 0,0,0,0,$new_width[0],$new_height[1],$image_size[0],$image_size[1]);
					}
					// sauvegarder l'image dans le dossier avatar_user avec comme nom "id_user.jpg"
					imagejpeg($image_finale, $url.'/'.$idUser.'.jpg');
				}
			}
		}
		echo $erreur;
	}

	/**
	 *
	 * @param unknown $adresse_mail
	 * @param unknown $sujet_mail
	 * @param unknown $message
	 */
	public static function envoie_mail($adresse_mail, $sujet_mail, $message) {
		$return_message = array ();
	
		if (! FonctionUtils::regex_email_passage_ligne ( $adresse_mail )) { // On filtre les serveurs qui présentent des bogues.
			$passage_ligne = "\r\n";
		} else {
			$passage_ligne = "\n";
		}
	
		// =====Création du header de l'e-mail.
		// A qui on envoie le mail.
		$to = $adresse_mail;
		// le sujet du email
		$sujet = $sujet_mail;
		// en-tete de l'email
		$headers = '';
		/*$headers .= 'Mime-Version: 1.0' . "\r\n";
		 $headers .= 'From: Nadji Karim <nadji.karim38@gmail.com>' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= "\r\n";*/
		$headers =  'MIME-Version: 1.0' . "\r\n";
		$headers .= 'From: Sygsyp sygsyp@alwaysdata.net' . "\r\n";
		//$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'Content-type:text/plain;charset=utf-8'."\r\n";
		$body = $message;
		
		$body .= "\n\n";
		$body .= "Administrateur SYG"."\n\n";
		$body .= "Email:  contact@secureyourgrowth.com"."\n";
			
		// Vérifier que le SMTP est bien configure
		set_error_handler ( function ($errno, $errstr, $errfile, $errline, array $errcontext) {
			if (0 === error_reporting ()) {
				return false;
			}
			throw new ErrorException ( $errstr, 0, $errno, $errfile, $errline );
		} );
	
			try {
				mail ( $to, $sujet, $body, $headers );
				$return_message ['status'] = 'success';
				return $return_message;
			} catch ( ErrorException $e ) {
				$return_message ['status'] = 'error';
				$return_message ['message'] = 31;
				return $return_message;
			}
	}

	/**
	 * Fonction controle de saisie clavier du NOM de l'utilisateur.
	 * $nom : La chaine de caractère qu'on veut vérifier.
	 * */
	public static function regex_nom_prenom ($nom)
	{
		// A revoir
		return preg_match('#^[a-zA-Z]$#', $nom);
	}

	/**
	 * Fonction controle de saisie clavier de la date.
	 * $date : La date de naissance qu'on veut v�rifier.
	 * */
	public static function regex_date($date)
	{
		return preg_match('#^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])$#', $date);
	}

	/**
	 * Fonction controle de saisie clavier de l'adresse email de l'utilisateur.
	 * $email : L'adresse email à vérifier.
	 * */
	public static function regex_email($email)
	{
		//return preg_match('#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#' , $email);
		return preg_match('#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$#',$email);
	}
	
	/**
	 * 
	 * @param unknown $email
	 * @return unknown
	 */
	public static function regex_email_passage_ligne($email) 
	{
		//return preg_match('#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#' , $email);
		return preg_match('#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#',$email);
	}
	
	/**
	 * Fonction contrôle de saisie clavier de l'adresse postal de l'utilisateur.
	 * $code_postal : L'adresse postal à vérifier.
	 * */
	public static function regex_code_postal($code_postal)
	{
		if(empty($code_postal)){
			return 1;
		}
		return preg_match('#^\d{5,5}$#', $code_postal);
	}

	/**
	 * Fonction contrôle de saisie clavier du numéro de téléphone de l'utilisateur.
	 * $num_tel : Le numéro de téléphone à vérifier.
	 * */
	public static function regex_num_tel($num_tel)
	{
		if(empty($num_tel)){
			return 1;
		}
		return preg_match('#^(\+[0-9]{3}?[0-9]{8})$|^(^0[0-9]{9})$#', $num_tel);
	}
}
?>
