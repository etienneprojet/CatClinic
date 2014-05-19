<?php

final class ChatMapper extends CorrespondanceTable implements CorrespondanceTableInterface
{
	public function __construct()
	{
		parent::__construct(Constantes::TABLE_CHAT);
		$this->_S_classeMappee = 'Chat';
	}

	public function trouverParIdentifiant ($I_identifiant)
	{
		if (isset($I_identifiant)) {
			$S_requete    = "SELECT id, nom, age, tatouage FROM " . $this->_S_nomTable .
	                        " WHERE id = $I_identifiant";
			$O_connexion  = ConnexionMySQL::recupererInstance();

			if ($A_chat = $O_connexion->projeter($S_requete))
			{
                $O_chatTemporaire = $A_chat[0];

				if (is_object($O_chatTemporaire)) {
					if (class_exists($this->_S_classeMappee)) {
						$O_chat = new $this->_S_classeMappee;

						$O_chat->changeIdentifiant($O_chatTemporaire->id);
						$O_chat->changeNom($O_chatTemporaire->nom);
						$O_chat->changeAge($O_chatTemporaire->age);
                        $O_chat->changeTatouage($O_chatTemporaire->tatouage);
					}
				}

				return $O_chat;
			}
			else
			{
				// Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
				throw new Exception ("Il n'existe pas de chat pour l'identifiant '$I_identifiant'");
			}
		}
		else
		{
			throw new Exception ("L'identifiant d'un chat ne peut être vide et doit être un entier");
		}
	}

	public function creer (Chat $O_chat)
	{
		if (!$O_chat->donneNom() || !$O_chat->donneAge() || !$O_chat->donneTatouage())
		{
			throw new Exception ("Impossible d'enregistrer le chat");
		}

		$S_tatouage = $O_chat->donneTatouage();
		$S_nom = $O_chat->donneNom();
		$I_age = $O_chat->donneAge();

		$S_requete = "INSERT INTO " . $this->_S_nomTable . " (nom, age, tatouage) VALUES ('$S_nom', $I_age, '$S_tatouage')";

		$O_connexion = ConnexionMySQL::recupererInstance();

		// j'insère en table et inserer me renvoie l'identifiant de mon nouvel enregistrement...je le stocke
		$O_chat->changeIdentifiant($O_connexion->inserer($S_requete));
	}

	public function actualiser (Chat $O_chat)
	{
		if (null != $O_chat->donneIdentifiant())
		{
			if (!$O_chat->donneNom() || !$O_chat->donneAge() || !$O_chat->donneTatouage())
			{
				throw new Exception ("Impossible de mettre à jour le chat");
			}

			$S_tatouage = $O_chat->donneTatouage();
			$S_nom = $O_chat->donneNom();
			$I_age = $O_chat->donneAge();
			$I_identifiant = $O_chat->donneIdentifiant();

			$S_requete = "UPDATE " . $this->_S_nomTable . " SET nom = '$S_nom', tatouage = '$S_tatouage', age = $I_age WHERE id = " . $I_identifiant;
			$O_connexion = ConnexionMySQL::recupererInstance();
			$O_connexion->modifier($S_requete);

			return true;
		}

		return false;
	}

	public function supprimer (Chat $O_chat)
	{
		if (null != $O_chat->donneIdentifiant())
		{
			// il me faut absolument un identifiant pour faire une suppression
			$S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = " . $O_chat->donneIdentifiant();
			$O_connexion = ConnexionMySQL::recupererInstance();

			// si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
			// attention donc à bien utiliser l'égalité stricte ici !
			if (false === $O_connexion->modifier($S_requete))
			{
				throw new Exception ("Impossible d'effacer le chat d'identifiant " . $O_chat->donneIdentifiant());
			}

			return true;
		}

		return false;
	}
}