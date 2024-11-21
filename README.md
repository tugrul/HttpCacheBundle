
# Tug HTTP Cache Bundle

The **Tug HTTP Cache Bundle** is a Symfony bundle that enhances HTTP caching by managing `ETag` and `Last-Modified` headers at the router and parameter level. It allows for fine-grained control over cacheable responses, optimizing caching for both client-side and proxy servers like Nginx and Varnish.

---

## Features

- Cache management by route and allowed parameters.
- Automatic handling of `ETag` and `Last-Modified` headers.
- Efficient validation for `304 Not Modified` responses.
- Seamless integration with proxy cache servers like Nginx and Varnish.
- Configurable global and route-specific caching behavior.

---

## Installation

Install the bundle using Composer:

```bash
composer require tugrul/http-cache-bundle
```

---

## Configuration

### Define a Cache Pool

First, define a cache pool for the bundle to store ETag and Last-Modified values:

```yaml
# config/packages/cache.yaml

framework:
  cache:
    pools:
      cache.tug_http_cache:
        adapter: cache.adapter.filesystem
```

###  Add Bundle Configuration

Next, configure the bundle according to your application's caching requirements:

```yaml
# config/packages/tug_http_cache.yaml

tug_http_cache:
  ignored_param_names:
    - worthless_param_name

  allowed_param_names:
    _locale: en
    amp: false

  routes:
    - name: index
    - name: blog_post_index
      allowed_query_names:
        page: 1  # default query value if not present in the request
    - name: blog_post_detail
      allowed_param_names:
        slug: null
```

### Configuration Options

- **`ignored_param_names`**: Parameters to ignore when building cache keys.
- **`allowed_param_names`**: Globally allowed parameters with their default values.
- **`routes`**: Route-specific cache configuration.
    - **`name`**: The route name.
    - **`allowed_query_names`**: Query parameters allowed for this route.
    - **`allowed_param_names`**: Parameters allowed for this route, with optional default values.

---

## Usage

Once configured, the bundle:

1. **Checks Cache**: For incoming requests, it checks if a cached response exists based on the route and allowed parameters.
2. **Validates Cache**: If the cache exists, it validates the `ETag` and `Last-Modified` headers to decide whether to return a `304 Not Modified` response.
3. **Captures Response**: For cacheable responses, it automatically generates `ETag` and `Last-Modified` headers and stores them for future use.

---

## Example

### Example Route

Define routes in your application:

```yaml
# config/routes.yaml

index:
  path: /
  controller: App\Controller\IndexController::index

blog_post_index:
  path: /blog
  controller: App\Controller\BlogController::index

blog_post_detail:
  path: /blog/{slug}
  controller: App\Controller\BlogController::detail
```

### Example Controller

Ensure your responses are cacheable:

```php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    public function index(): Response
    {
        $response = $this->render('blog/index.html.twig');
        $response->setPublic();
        $response->setMaxAge(3600); // Cache for 1 hour

        return $response;
    }

    public function detail(string $slug): Response
    {
        $response = $this->render('blog/detail.html.twig', ['slug' => $slug]);
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
```

### Proxy Server Configuration

For Nginx or Varnish, ensure they are configured to respect `ETag` and `Last-Modified` headers for caching.

---

## Contributing

Contributions are welcome! Please submit issues or pull requests via the [GitHub repository](https://github.com/tugrul/HttpCacheBundle).

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## Support

If you encounter any issues or have questions, feel free to open an issue on GitHub or contact the maintainer directly.

