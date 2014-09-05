<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('78-Search-APIs');
$entry->setTitle('Search APIs');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1118026860);
$entry->setUpdated(1118067377);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Twice in the past week I found myself needing to create or alter database
    search functionality in some APIs for work. In doing this work, I discovered
    some techniques that make this process much easier. In line with <em>The
        Pragmatic Programmer</em>, I found myself exploring the DRY principle
    (Don't Repeat Yourself), and looking into code generation (this time, SQL)
    -- basically to exploit my inherent laziness and to make my job easier.
</p>
<p> 
    I thought I'd share some of the principles I've discovered for myself as I
    haven't read much information on the subject. Some of this may be
    rudimentary for some readers or those who work with more advanced
    abstraction layers (I suspect <a
        href="http://pear.php.net/packages/DB_DataObject">DB_DataObject</a> may
    do much of this), but hopefully the information can be a useful reference
    for others (myself included).
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h4>1. Store individual criteria and joins in a class array</h4>
<p>
    Determine all the various types of criteria and joins you want to allow in
    searches, and store them in a class array, one each for the WHERE and JOIN
    clauses. For example:
</p>
<pre>
var $criteria = array(
    'title'    => 'title = ?',
    'author'   => 'user.username = ?',
    'keyword'  => 'MATCH(body) AGAINST (?)',
    'date'     => 'when_modified BETWEEN ? AND ?',
    'category' => 'category IN (!)'
);
var $join = array(
    'author'   => 'INNER JOIN user ON user.id = article.author_id',
    'category' => 'INNER JOIN category ON category.id = article.category'
);
</pre>
<p>
    This puts all your criteria in one place, and will later allow you to loop
    over each set of criteria in order to build your SQL. More on that later.
</p>
<h4>2. Pass search criteria as an associative array</h4>
<p>
    The first tip is to pass your search criteria as an associative array to
    your method. The keys for the array should be the field (or metafield) you
    wish to search on, and the value the actual value(s) for the comparison. As
    an example, let's say I want to search on the 'author' field and the title
    field; I might then pass an associative array like the following:
</p>
<pre>
    $criteria = array('title' => 'Cgiapp', 'author' => 'weierophinney');
</pre>
<p>
    By passing criteria as an associative array, you build in some automatic
    flexibility for the system. You can check for certain keys being present,
    and build up your WHERE clause based only on what is present. You can also
    set default values that are overridden only if a key is passed (or if the
    value for that key is outside a certain range). 
</p>
<p>
    Additionally, if you setup your keys to match those from (1), above, you'll
    be able to loop through those keys to determine what criteria to utilize. A
    full example will be shown below.
</p>
<h4>3. Set up default criteria</h4>
<p>
    As mentioned in (2), you may need some default criteria. For instance,
    you may want to only select active records -- unless the 'inactive' key is
    passed. Or you may want to reduce the performance hit on your DB server by
    introducing limits and offsets -- so you will specify default values for
    'offset' and 'limit'. Another likely candidate is for determining sort
    order.
</p>
<p>
    Default values come in especially handy when needing to do both a list and a
    search functionality for your API; you can use the same method, but when
    listing, you simply use default values instead of passing search criteria.
</p>
<p>
    The most likely place to keep these default values is, again, in a class
    array. You may notice that we're starting to get some overlap in these
    arrays -- we've got a criteria array, a join array, and now one for default
    values. How about combining them (and adding default constraints for date,
    offset, limit, and sort):
</p>
<pre>
var $criteria = array(
    'title'    => array(
        'where' => 'title => ?'
    ),
    'author'   => array(
        'where' => 'user.username = ?',
        'join'  => 'INNER JOIN user ON user.id = article.author_id'
    ),
    'keyword'  => array(
        'where' => 'MATCH(body) AGAINST (?)'
    ),
    'date'     => array(
        'where'   => 'when_modified BETWEEN ? AND ?',
        'default' => array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d'))
    ),
    'category' => array(
        'where' => 'category IN (!)'
    ),
    'offset' => array(
        'default' => 0
    ),
    'limit' => array(
        'default' => 25
    ),
    'sort' => array(
        'default' => 'title ASC'
    )
);
</pre>
<h4>4. Store criteria and values in arrays during processing</h4>
<p>
    As you parse through the associative array of criteria, you will build the
    bits and pieces of the WHERE clause by pulling them from the $constraint
    array (or the 'where' keys of the $constraint array). Usually these are
    statements like:
</p>
<ul>
    <li>author = ?</li>
    <li>title = ?</li>
    <li>category IN (!)</li>
    <li>MATCH(keyword) AGAINST (?)</li>
</ul>
<p>
    Store each WHERE clause as an individual entry in a local $where array.
    Additionally, if you use placeholders (you <em>do</em> use placeholders,
    right?), push these onto a separate $params array:
</p>
<pre>
    $where  = array();
    $params = array();

    // A simple example
    if (!empty($criteria['author'])) {
        $where[]  = 'user.username = ?';
        $params[] = $criteria['author'];
    }

    // More complex, but more automated; $search is the passed in associative
    // array of search criteria
    foreach ($this->criteria as $field => $info) {
        if (!empty($search[$field])) {
            $where[] = $info['where'];
            if (!is_array($search[$field]) {
                $params[] = $search[$field];
            } else {
                foreach ($search[$field] as $value) {
                    if (is_scalar($value) {
                        $params[] = $value;
                    }
                }
            }
        }
    }
</pre>
<p>
    Then, when you're finished, you can build your WHERE clause by imploding the
    array:
</p>
<pre>
    $whereSQL = implode(' AND ', $where);
</pre>
<h4>5. Keep track of JOINs</h4>
<p>
    Where I work, we're often performing JOIN operations on link tables.
    Sometimes, multiple sets of criteria will require the same JOIN, other times
    no joins will be used, and still other times we will need multiple JOINs.
    The obvious challenges are that you only want <em>one</em> JOIN of each type
    to be performed, and you don't want to perform unnecessary JOINs.
</p>
<p>
    You can simplify the situation by defining an array that keeps track of
    JOINs that have been performed. Then, have a method to which you pass a JOIN
    statement, and have that method attempt a lookup; if no matches are found in
    the array, add it.
</p>
<p>
    Then, as you parse through the criteria, call the method whenever a JOIN is
    needed.  As an example:
</p>
<pre>
// Private class array property to keep track of joins
var $_joined = array();

// Private function to keep track of JOINs
function _joinTables($join)
{
    if (in_array($join, $this->_joined)) {
        return true;
    }
    $this->_joined[] = $join;
    return true;
}

// in search method:
if (!empty($info['join'])) {
    $this->_joinTables($info['join']);
}
</pre>
<h4>6. Allow retrieval of record counts only</h4>
<p>
    For pagination or informational purposes, you often need a count of records
    that match. You can use the same search method to generate this information
    by checking for a 'COUNT_ONLY' key in your search array:
</p>
<pre>
$select = 'id, user.username, title, body, category';
if (isset($search['COUNT_ONLY'])) {
    $select = 'COUNT(id)';
}
</pre>
<p>
    If you're using LIMIT clauses, you may also want to override the LIMIT
    clause at this same step ("$limitSql = '';").
</p>
<h4>7. Putting it together</h4>
<p>
    So, what might it look like all put together? Here's an example:
</p>
<pre>
class Articles
{
    // Array of criteria; each element key points to an array that contains one
    // or more of the following keys: 'where', 'join', and 'default'.
    var $criteria = array(
        'title'    => array(
            'where' => 'title => ?'
        ),
        'author'   => array(
            'where' => 'user.username = ?',
            'join'  => 'INNER JOIN user ON user.id = article.author_id'
        ),
        'keyword'  => array(
            'where' => 'MATCH(body) AGAINST (?)'
        ),
        'date'     => array(
            'where'   => 'when_modified BETWEEN ? AND ?',
            'default' => array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d'))
        ),
        'category' => array(
            'where' => 'category IN (!)'
        ),
        'offset' => array(
            'default' => 0
        ),
        'limit'  => array(
            'default' => 25
        ),
        'sort' => array(
            'default' => 'title ASC'
        )
    );

    // Array for keeping track of JOINs
    var $_joined = array();

    // Function for adding JOINs to the stack
    function _joinTables($join)
    {
        if (in_array($join, $this->_joined)) {
            return true;
        }
        $this->_joined[] = $join;
        return true;
    }

    // Actual search method
    function search($search)
    {
        $where  = array();
        $join   = array();
        $params = array();

        // Get criteria
        foreach ($this->criteria as $field => $info) {
            if (!empty($search[$field])) {
                // Get WHERE clause, if necessary
                if (!empty($info['where'])) {
                    $where[] = $info['where'];

                    // Get placeholder values:
                    $value = $search[$field];
                    if (is_scalar($value)) {
                        $params[] = $value;
                    } elseif (is_array($value)) {
                        // Sometimes we need multiple placeholders for a single
                        // piece of criteria
                        foreach ($value as $val) {
                            if (is_scalar($val)) {
                                $params[] = $val;
                            }
                        }
                    }
                }

                // Get JOIN clause, if necessary
                if (!empty($info['join'])) {
                    $this->_joinTables($info['join']);
                }
            }
        }

        $joinSql  = implode("\n", $this->_joined);
        $whereSql = implode(' AND ', $where);

        // How many records should we get, starting from where?
        $offset = $criteria['offset']['default'];
        $limit  = $criteria['limit']['default'];
        if (!empty($search['offset'])) {
            $offset = $search['offset'];
        }
        if (!empty($search['limit'])) {
            $limit = $search['limit'];
        }
        $limitSql = "LIMIT $offset,$limit";
        if ($limit < 1) {
            // If limit is less than one, assume we want to pull all records
            $limitSql = '';
        }

        // sort order?
        $sort = $criteria['sort']['default'];
        if (!empty($search['sort'])) {
            $sort = $search['sort'];
        }

        // Is this a count operation?
        $select = 'id, title, user.username, body, when_modified, category';
        if (isset($search['COUNT_ONLY'])) {
            $select   = "COUNT(id)";
            $limitSql = ''; // No limit necessary for a count operation
        }

        // Build SQL
        $sql =<<<EOQ
SELECT
    $select
FROM
    articles
$joinSql
WHERE
    $whereSql
ORDER BY $sort
$limitSql
EOQ;
        // And now we can pull our records or count...
        // This example uses PEAR::DB, and assumes a DB connection stored in the
        // $db class property.
        if (isset($search['COUNT_ONLY'])) {
            $results = $this->db->getOne($sql, $params);
        } else {
            $results = $this->db->getAll($sql, $params, DB_FETCHMODE_ASSOC);
        }
        if (PEAR::isError($results)) {
            return "Error!";
        }

        return $results;
    }
}
</pre>
<h4>Final Notes</h4>
<p>
    The above method is incomplete. It doesn't do any validation on the values
    coming in (for instance, offset and limit should be unique; sort should
    probably verify that the field is valid, and that a directional directive is
    passed; and various values for the other criteria should be sanitized). It
    only does 'AND' style criteria; what if you want to do 'OR'? or mix and
    match AND and OR? What if you have a list of values for an IN () style
    statement -- how should those be passed to the API and processed?
</p>
<p>
    However, for most situations I've encountered, the above functionality is
    more than adequate (in some cases, overkill). It provides a simple,
    generalized solution towards searching that is extremely flexible. If
    special cases are necessary, it would be possible to setup a callback
    system, if necessary.
</p>
<p>
    It's possible that this example duplicates the efforts of projects like
    PEAR's DB_DataObjects. I've had little time to look into that project; if
    somebody reading this could comment, I'd appreciate it.
</p>
<p>
    As a final note, I want to address the issue of validation. I personally use
    the MVC pattern, and the search algorithm above is part of the Model. As
    such, I typically do not waste much effort on validation at this level of
    the application; validation is the realm of the Controller, which should be
    filtering the request before passing it on to the Model. However, in some
    situations, this would not be ideal (I could, for instance, see an AJAX
    style application communicating directly to the API via ReST), and
    precautions should be taken based on such situations.
</p>
<p>
    <em>Thanks to <a href="http://paul-m-jones.com/">Paul M. Jones</a> for
        encouraging me to write this up.</em>
</p>
EOT;
$entry->setExtended($extended);

return $entry;