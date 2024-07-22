# Modelos

## User

<pre>
username: VARCHAR(32) REQUIRED
firstname: VARCHAR(32) REQUIRED
lastname: VARCHAR(32) REQUIRED
email: VARCHAR(128) REQUIRED
phone: VARCHAR(32) REQUIRED
password: VARCHAR(2048) REQUIRED
role: VARCHAR(32)
created (DEFAULT CURRENT_TIMESTAMP) NOT EDITABLE
</pre>

---

## Turno

<pre>
client_id: ID REQUIRED
client_name: VARCHAR(64)
editedBy: text
description: text
cost: decimal(20,2)
items_id: text [array] - "id1,id2,id3"
day: date REQUIRED
startTime: time REQUIRED
duration: VARCHAR(8) - max: 9999 minutos REQUIRED
location: VARCHAR(16) - [ peluquería , gabinete, ambos ]
status: VARCHAR(16) - [ pending , confirmed , cancelled ]
</pre>

---

## Cliente

<pre>
name: VARCHAR(64) REQUIRED
birthday: date
dni: VARCHAR(10)
phone: VARCHAR(32)
phone_contact: VARCHAR(32)
created (DEFAULT CURRENT_TIMESTAMP) NOT EDITABLE
</pre>

---

## Items

(Servicios y productos)

<pre>
name: VARCHAR(64) REQUIRED
type: VARCHAR(16) REQUIRED
categories: text [array]
description: text
cost: decimal(20,2)
created (DEFAULT CURRENT_TIMESTAMP) NOT EDITABLE
</pre>

---

## Charge

<pre>
client_id: ID
client_name: VARCHAR(64)
turn_id: ID
description: text
cost: decimal(20,2) REQUIRED
status: VARCHAR(16) - [ pending , completed , cancelled ]
date_created: datetime
</pre>
