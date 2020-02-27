
# Wp Rest Api

A custom wp api

### Requirements
- PHP >= 7.1
- [Wp-Helper](https://github.com/NicolasBernaux/wp-helper)

### Wp Rest API endpoints
```
GET: /wp-json/api-rest/v1/listing
```

#### Get data from a slug
```
GET: /wp-json/api-rest/v1/slug/<your-slug>
```

#### Menus

```
GET: /wp-json/api-rest/v1/menu
GET: /wp-json/api-rest/v1/menu?params=<params>
```
You can use every params form [wp_get_nav_menu_items()](https://developer.wordpress.org/reference/functions/wp_get_nav_menu_items/)
