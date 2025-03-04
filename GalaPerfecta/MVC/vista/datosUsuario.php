<?php

$control = new inicioControladorCargaLogin();
$control->validarUser();

$idDespuesDelLogin = $control->getIDeUsuarioDeVerdad();
$_SESSION['idUsuario'] = $idDespuesDelLogin; // Guardar el ID en la sesión

?>