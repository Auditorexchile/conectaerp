<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = 'Chat Auditor IA';
?>
<div class="ia-module chat-module">
    <div class="module-header">
        <h1>💬 Chat Auditor IA</h1>
        <p>Asistente especializado en auditoría financiera</p>
    </div>
    <div class="chat-container">
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                <div class="avatar">🤖</div>
                <div class="text">
                    <strong>Auditor IA</strong>
                    <p>Hola! Soy tu asistente de auditoría. ¿En qué puedo ayudarte hoy?</p>
                    <small>Puedes preguntarme sobre auditorías, normativa, IFRS, impuestos, etc.</small>
                </div>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Escribe tu pregunta..." onkeypress="if(event.key==='Enter')sendMessage()">
            <button class="btn btn-primary" onclick="sendMessage()">Enviar</button>
        </div>
    </div>
</div>
<script>
function sendMessage(){
    var input=document.getElementById('chatInput');
    var msg=input.value.trim();
    if(!msg)return;
    var container=document.getElementById('chatMessages');
    container.innerHTML+='<div class="message user"><div class="avatar">👤</div><div class="text"><p>'+msg+'</p></div></div>';
    input.value='';
    setTimeout(function(){
        container.innerHTML+='<div class="message bot"><div class="avatar">🤖</div><div class="text"><strong>Auditor IA</strong><p>Procesando tu consulta...</p></div></div>';
        container.scrollTop=container.scrollHeight;
    },500);
}
</script>
