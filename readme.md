# Modelos

## User

<pre>
username: VARCHAR(32)
firstname: VARCHAR(32)
lastname: VARCHAR(32)
email: VARCHAR(128)
phone: VARCHAR(32)
password: VARCHAR(2048)
role: VARCHAR(32)
created (DEFAULT CURRENT_TIMESTAMP)
</pre>

---

## Turno

<pre>
client_id: ID
client_name: VARCHAR(64)
description: text
formula: text [array] // Ej_formula: "tintura|25|silkey|7.2,polvo|15|nomeacuerdo,oxidante|80|iyosey"
cost: decimal(20,2)
items_id: text [array] - "id1,id2,id3"
day: date
startTime: time
duration: VARCHAR(8) - max: 9999 minutos
location: VARCHAR(16) - [ peluquería , gabinete, ambos ]
status: VARCHAR(16) - [ pending , confirmed , cancelled ]
</pre>

El color del turno va a estar determinado por el status (pendiente ó confirmado) y la location (peluquería , gabinete, ambos).

### _Fórmula_

<pre>
type: [ polvo, oxidante , tintura ]
grams: número gr
brand: [ silkey , cav , etc. ]
color_num: número Ej: 7.2 (sólo para tintura)

Ej_formula:
"tintura|25|silkey|7.2,polvo|15|nomeacuerdo,oxidante|80|iyosey"
</pre>

---

## Cliente

<pre>
name: VARCHAR(64)
birthday: date
dni: VARCHAR(10)
phone: VARCHAR(32)
phone_contact: VARCHAR(32)
created (DEFAULT CURRENT_TIMESTAMP)
</pre>

---

## Items

(Servicios y productos)

<pre>
name: VARCHAR(64)
type: VARCHAR(16)
categories: text [array]
description: text
cost: decimal(20,2)
created (DEFAULT CURRENT_TIMESTAMP)
</pre>

---

## Charge

<pre>
client_id: ID
client_name: VARCHAR(64)
turn_id: ID
cost: decimal(20,2)
status: VARCHAR(16) - [ pending , confirmed , cancelled ]
date_created: datetime
</pre>
