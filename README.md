# Query Builder Symfony

Generates queries dynamically on a database.
The tool allows to set up the configuration of a database in a YML file.

A developer can modify certain values ​​in the configuration file to, for example, translate fields from a table.

The applicant Builder takes as input a json file with the fields to request as well as the conditions.
From this file it will construct the query, execute it and return the result.

## Installation

- Install the vendor package
```
composer require littlerobinson/query-builder-bundle
```

- Create the route 

Use the bundle route :

```
# /app/config/routing.yml

LittlerobinsonQueryBuilderBundle:
    resource: "@LittlerobinsonQueryBuilderBundle/Resources/config/routing.yml"
    prefix:   /querybuilder
```

Or Create your own controller and view (with annotation) :

```php
# /AppBundle/Controller/YourController.php

/**
 * @Route("/querybuilder", name="query_builder")
 * @return Response
 */
public function indexAction()
{
    return $this->render('querybuilder/index.html.twig');
}

/**
 * @Method("POST")
 * @Route("/querybuilder/query", name="query_builder_query")
 * @return Response
 */
public function queryAction()
{
    $run = RunQueryBuilder::getInstance($this->container);
    $run->execute();
    return new Response();
}
```

And customize the template :

```twig
# /app/Resources/views/index.html.twig
{% extends 'LittlerobinsonQueryBuilderBundle:QueryBuilder:query_layout.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}"/>
{% endblock %}

{% block extra %}
    <div class="query_builder_menu">
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                            aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Gestion des inscrits</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="{{ path('registrant_index') }}">Accueil</a></li>
                        <li><a href="{{ path('query_builder') }}">Requêteur</a></li>
                        <li><a>{{ app.user.firstName }} {{ app.user.Lastname }}</a></li>
                        <li><a href={{ path('logout') }}>Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
{% endblock %}
```




- Create the symlink for assets
```
php bin/console assets:install --symlink
```

- Add configuration
```
# /app/config/config.yml

littlerobinson_query_builder:
    database:
        title: Software name
        is_dev_mode: false
        config_path: database-config.yml
        file_name: querybuilder_db_name
        params:
            driver: pdo_mysql
            host: 127.0.0.1
            port: 3306
            user: root
            password: root
            dbname: database_name
            charset: utf8mb4
    user: { name: user_id, type: cookie }
    association: { name: group_id, type: cookie }
    rules:
        user_id: { type: cookie }
    security:
        database:
            post: post.user
```
- Set restriction (cookie or session)
```
user_id = 1 OR user_id = [1,2]
```

### Configuration file

When executing the `writeDatabaseYamlConfig` method it will generate a configuration YAML file with a retro engineering of your database.
You can change :
- table name (_table_translation)
- table visibility (__table_visibility)
- field name (__field_translation)
- field visibility (__field_visibility)

```yaml
post:
    _table_translation: Article
    _table_visibility: true
    _primary_key: 
        - id
    id:
        name: id
        _field_translation: 'Identifiant'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    category_id:
        name: category_id
        _field_translation: 'Catégorie'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    user_id:
        name: user_id
        _field_translation: 'Utilisateur'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    title:
        name: title
        _field_translation: 'Titre'
        _field_visibility: true
        type: string
        default: null
        length: 50
        not_null: true
        definition: null
    _FK:
        category_id:
            tableName: category
            columns: category_id
            foreignColumns: id
            name: FK_2F4B2CA110298215
            options: { onDelete: null, onUpdate: null }
        user_id:
            tableName: registrant
            columns: user_id
            foreignColumns: id
            name: FK_2F4B2CA13304A716
            options: { onDelete: null, onUpdate: null }
            
category:
    _table_translation: Catégorie
    _table_visibility: true
    _primary_key: 
        - id
    id:
        name: id
        _field_translation: 'Identifiant'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    title:
        name: name
        _field_translation: 'Nom'
        _field_visibility: true
        type: string
        default: null
        length: 100
        not_null: true
        definition: null
        
user:
    _table_translation: Utilisateur
    _table_visibility: true
    _primary_key: 
        - id
    id:
        name: id
        _field_translation: 'Identifiant'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    group_id:
        name: group_id
        _field_translation: 'Groupe'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    firstname:
        name: firstname
        _field_translation: 'Prénom'
        _field_visibility: true
        type: string
        default: null
        length: 100
        not_null: true
        definition: null
    lastname:
        name: lastname
        _field_translation: 'Nom'
        _field_visibility: true
        type: string
        default: null
        length: 100
        not_null: true
        definition: null
    _FK:
        group_id:
            tableName: group
            columns: group_id
            foreignColumns: id
            name: FK_2F4B2CA110298216
            options: { onDelete: null, onUpdate: null }
    
group:
    _table_translation: Groupe
    _table_visibility: true
    _primary_key: 
        - id
    id:
        name: id
        _field_translation: 'Identifiant'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    name:
        name: name
        _field_translation: 'Nom du groupe'
        _field_visibility: true
        type: string
        default: null
        length: 50
        not_null: true
        definition: null****
            
comment:
    _table_translation: Commentaire
    _table_visibility: true
    _primary_key: 
        - id
    id:
        name: id
        _field_translation: 'Identifiant'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    post_id:
        name: post_id
        _field_translation: 'Article'
        _field_visibility: true
        type: integer
        default: null
        length: null
        not_null: true
        definition: null
    content:
        name: content
        _field_translation: 'Commentaire'
        _field_visibility: true
        type: string
        default: null
        length: 50
        not_null: true
        definition: null
    _FK:
        post_id:
            tableName: post
            columns: post_id
            foreignColumns: id
            name: FK_2F4B2CA110298256
            options: { onDelete: null, onUpdate: null }
```

You can also add rules for automatic restrictions.
Example : you want someone just watch post from a specific user.

```yaml
post:
    _table_translation: Article
    _table_visibility: true
    _primary_key: id
    _rules: {
        user_id: 1
    }
    id:
        name: id
...
```
### Security

Add this in the config.yml file to tell the program where to find the restriction value.

```yaml
# config.yml
user: { name: user, type: cookie }
association: { name: group, type: cookie }

rules:
    group: { type: cookie }
...
```

You can add security restriction like this in the security.yml file.

```yaml
# security.yml
database:
    category:
        post_id.user_id: user_id
    tag: # Many to Many relation
        post_tag.post_id.user_id: user_id
...
```

### Request

When you execute a request it will generate a json value representing the query.

```json
{  
   "from":{  
      "post":{  
         "title":"title",
         "category_id":{  
            "id":"id",
            "name":"name"
         },
         "user_id":{  
             "firstname":"firstname",
             "lastname":"lastname",
             "group_id":{
                "name":"name"
             }
          },
         "id":"id"
      }
   },
   "where":[  
     {  
        "AND":{  
           "group.name":{  
              "EQUAL":[  
                 "ADMIN"
              ]
           }
        }
     }
  ]
}
```

### Output

```mysql
SELECT 
    post.title AS post_title, 
    category.id AS category_id,
    category.name AS category_name,
    user.firstname AS user_firstname,
    user.lastname AS user_lastname,
    group.name AS group_name,
    post.id AS post_id
FROM
    post AS post
LEFT jOIN
    category as category
ON 
    post.category_id = category.id
LEFT JOIN 
    user AS user
ON 
    post.user_id = user.id
LEFT JOIN
    group AS group
ON
    user.group_id = group.id
WHERE
    group.name = 'ADMIN';
```

### Tests
```
phpunit --bootstrap vendor/autoload.php  tests/
```

### IHM

IHM is cutting in 3 zones :
- appRequest : It's a parent zone for making the request.
 It's include 2 - zones :
    - SelectItem : zon of selecting table and rows
    - ConditionItem : Zone to build request conditions
    - SpreadSheet : Zone for showing research result with grid table

Javascript Variables list in appRequest :
- dbObj : object representation of the JSON database configuration
- foreignTables : List of foreign tables
- items : Object representation of selectable table and rows with checked status and traduction name
- from : Object representing from request (for json query)
- where : Object representing where request (for json query)
- conditions : Array of objects representing conditions request
- columns : column result list with translation
- data : result data 
- jsonQuery : json query 
- sqlRequest : request query
