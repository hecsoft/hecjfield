IMPLEMENTATION :

* Copier les répertoires dans votre composant en respectant l'arborescence
* Ajouter le champ dans le XML de votre form (exemple dans models/forms/filexplorer.xml)
	<field 
		name="documents" 
		type="filexplorer" 
		default="/images/"  
		label="Mes documents"
		description="Parcourir mes documents" 
		mode="file" 
		filter="xls|xlsx|jpg|pdf|png|doc|docx" 
		exclude="powered_by.png"
	/> 
	
	name : Nom et id du champ
	type : filexplorer
	default : répertoire de base par défaut
	label : Etiquette
	description : Description
	mode : mode de sélection (file, folder , file&folder)
	filter : Liste des extensions a afficher separé par des |
	exclude : Liste des fichiers ou extension (mettre *.ext) a exclure
* Modifier le controller webservice et remplacer {NOM DU COMPOSANT} par le nom de votre composant
* Ajouter le CSS de jqueryUI si pas déjà présent dans le default.php de la vue utilisant ce champ
	$doc->addStyleSheet("//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css");