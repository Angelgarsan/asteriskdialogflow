# asteriskdialogflow
Integration ASTERISK-DIALOGFLOW


Install dependencies:
composer require google/apiclient
composer require league/oauth2-client
composer require gietos/phpagi
composer install


Replace YOUR_PROJECT_ID for your projectID on dialog.php
You need to download the service account JSON from Google Cloud and specify the path in the dialog.php file


Dialplan:
exten => 1004,1,Answer()
exten => 1004,n,agi(googletts.agi,"Luego de la señal, por favor di en que puedo ayudarte")
exten => 1004,n(init),agi(speech-recog.agi,es-DO)
exten => 1004,n,GotoIf($["${confidence}" > "0.6"]?success:retry)
exten => 1004,n(retry),agi(googletts.agi,"Puedes repetir?, no te he escuchado bien")
exten => 1004,n,goto(init) 
exten => 1004,n(success),Set(question=${utterance})
exten => 1004,n,AGI(dialog.php)
exten => 1004,n,Noop(${responsedialogflow})
exten => 1004,n,agi(googletts.agi,${responsedialogflow})
exten => 1004,n,agi(googletts.agi,"Pulsa 1 si tienes alguna otra pregunta")
exten => 1004,n,Read(digito,,1,,,3)
exten => 1004,n,GotoIf($["${digito}" == "1"]?stop:next)
exten => 1004,n(stop),agi(googletts.agi,"En que otra cosa puedo ayudarte?")
exten => 1004,n,goto(init) 
exten => 1004,n(next),agi(googletts.agi,"Hasta pronto")
