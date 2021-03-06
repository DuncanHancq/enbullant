<?php

class Template
{
	public $fileTemplate;
	public $tag = array();
	public $txt = array();

	public function __construct(string $fileTemplate)
	{
		$this->fileTemplate = $fileTemplate;
	}

	public function replaceContent(string $tag, string $txt)
	{
		$this->tag[] .= $tag;
		$this->txt[] .= $txt;
	}

	public function display()
	{
		ob_start(); // on entrepose temporairement les données
		include('inc/front/header.html');
		if(isset($_SESSION['user']))  // ligne de vérif "Admin"
		{
			include('inc/front/nav-bo.html');
		}
		include('inc/front/nav.html');
		include($this->fileTemplate);
		include('inc/front/footer.html');
		$template = ob_get_contents(); // On stocke dans template le contenu temporaire : mémoire tampon.
		ob_end_clean(); // On vide la mémoire tampon.

		$template = str_replace($this->tag,$this->txt,$template);
		// remplacement des tags dans le template appelé

		return $template;

	}


}
