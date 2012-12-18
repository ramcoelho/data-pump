Title Of My Data Migration
==========================

This is an optional block.

The parser only cares about what is written between brackets,
curly brackets and the indented lines that immediately follows them.

Brackets define variable. Here are comprehensive list of possible
variables:

* **o-set**

	*Origin setup*

	SQL statement to run on origin database before
	migration starts.

	Is an error occurs during setup, the migration proccess is
	aborted.
	
	This is a global purpose variable.

* **o-tear**

	*Origin teardown*

	SQL statement to run on origin database
	after migration ends.
	
	The migration proccess is not aborted if an error occurs during teardown.

	This is a global purpose variable.

* **d-set**

	*Destination setup*

	SQL statement to run on destination database
	before migration starts.

	Is an error occurs during setup, the migration proccess is
	aborted.
	
	This is a global purpose variable.

* **d-tear**

	*Destination teardown*

	SQL statement to run on destination database
	after migration ends.

	The migration proccess is not aborted if an error occurs during teardown.
	
	This is a global purpose variable.

* **t-set**

	*Table setup*

	SQL statement to run on destination database
	before a specific table migration starts.

	The statement can affect as many tables you want. You don't even
	have to touch the actual block table.
	
	The migration proccess is not aborted if an error occurs during a
	table step, but if it occurs on table setup, the engine will skip
	that table.

	This is a table specific variable.

* **t-tear**

	*Destination teardown*

	SQL statement to run on destination database
	after a specific table migration ends.

	The statement can affect as many tables you want. You don't even
	have to touch the actual block table.

	The migration proccess is not aborted if an error occurs during a
	table step.
	
	This is a table specific variable.

* **t-trans**

	*Destination translate function*

	This is a PHP code that you want to run on a TUPLE
	basis.

	YOU SHOULD AVOID THIS as it WILL SLOW DOWN
	YOUR DATA TRANSFER.

	Address the `$data` array to translate the data
	BEFORE the insert.
	
	YOU SHOULD AVOID CHANGING DATA ON THE ORIGIN DATABASE.

	The migration proccess is not aborted if an error occurs during a
	table step, but if it occurs on tuple translation, the engine will
	skip that tuple.

	This is a table specific variable.

* **rst**

	*Table reset*

	SQL statement to clean up the destination table
	before migration to avoid data duplication.

	The migration proccess is not aborted if an error occurs during a
	table step, but if it occurs on table reset, the engine will skip
	that table.
	
	This is a table specific variable.

* **imp**

	*Table import*

	The SELECT command that will run on ORIGIN database.
	You MUST 

	The migration proccess is not aborted if an error occurs during a
	table step, but if it occurs on table import, the engine will skip
	that table.
	
	This is a table specific variable.


Curly brackets define table blocks.

Every table block is linked to a **destination** table. The
data migration proccess is destination oriented and we are proud
of it. Please inform if you have found a migration case that is
origin oriented, because we never heard about one.

Once a table block is defined, whatever variables is set will
apply to that table only.

If you need to set global purpose variables, do it before setting
a table block. Preferably on this section. See example below.

Origin
------

Catalog **Whatever** on **A Server** (An IP or Something).

Destination
-----------

Catalog **Whatever** on **Other Server** (An IP or Something).

Setup SQL statement to run on origin server [o-set]
---------------------------------------------------

Note the *o-set* between the brackets above. You can change
everything on that line but that. The parser will look for
the first indented (tab or 4 spaces) right after it and use it
to use as the **Origin setup statement**.
This very block of text (wich you are reading now) is optional
and, as all unindented blocks, will be entirely ignored by the
parser. This one below, though, will be assigned to the **o-set**
parameter:

	-- SQL Comment

Destination Setup Statement [d-set]
-----------------------------------

	-- SQL Comment
	
Destination Teardown Statement [d-tear]
---------------------------------------

	-- SQL Comment

Origin Teardown Statement [o-tear]
----------------------------------

	-- SQL Comment

* * *

A Table Description {atable}
============================

Some comment for the **atable** table that will be ignored by the
parser.

The *atable* text between curly brackets above sets a table block.
The *rst* and *imp* blocks ahead will be applied on **atable** only.


You can describe the fields, or anything else you think is important.
Remember this is a Markdown file, so you can use it to document your
data migration proccess.

* **id** - Surrogate key
* **field1** - A field
* **field2** - Another field

Reset Command [rst]
-------------------

	TRUNCATE TABLE atable

Import Statement [imp]
----------------------

This will run on the origin server, but you MUST name the column
after the destination table fields, as they will be inserted
accordingly.

Any field not mentioned will be omitted from the INSERT statement.

	SELECT
		origin_field AS field1,
		another_origin_field AS field2
	FROM
		OriginTable

* * *

You Can Go On With Another Destination Table {anothertable}
===========================================================

You can repeat everything for **anothertable**.

* * *

Finally
=======

If you parse this file, it will generate the following parameter
array *(listed here for educational purpose only)*:

	$migration_parameters = array(
		'o-set' => '-- SQL Comment',
		'd-set' => '-- SQL Comment',
		'd-tear' => '-- SQL Comment',
		'o-tear' => '-- SQL Comment',
		'atable' => array(
			'rst' => 'TRUNCATE TABLE atable',
			'imp' => 'SELECT origin_field AS field1, another_origin_field AS field2 FROM OriginTable'
		),
		'anothertable' => array(
		)
	)