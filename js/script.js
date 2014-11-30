function agregarFecha()
{
	var fecha = document.getElementById('idFecha').value;

	document.getElementById('frmEvento').action = "l_evento.php?fecha=" + fecha;
}

function actualizarEvento()
{
	var fecha = document.getElementById('idFecha').value;
	var id = document.getElementById('idEvento').value;

	document.getElementById('frmEvento').action = "l_evento.php?fecha=" + fecha+"&id=" + id;
}

function limpiarTextArea(){
	var textArea = document.getElementById('idDetalle');
	textArea.value = "";
}