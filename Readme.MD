# CodeMediator - A Modern, Secure PHP Framework 🚀

**CodeMediator** is a lightweight yet powerful **MVC-based PHP framework** designed to streamline web development. Built with security, simplicity, and performance in mind, it provides developers with built-in libraries, helpers, and APIs to accelerate development without compromising security.

### 🔥 Features
✔ **MVC Architecture** – Clean and structured codebase  
✔ **Built-in Security** – Protects against **XSS, CSRF, Session Hijacking**, and more  
✔ **Powerful Router** – Default routing + customizable dynamic routes  
✔ **Middleware Support** – Easily intercept and modify requests/responses  
✔ **Database Abstraction** – Secure database interactions with minimal code  
✔ **Caching System** – Improve performance with built-in caching  
✔ **Configurable** – Set `base_url`, timezone, environment (dev/prod), 404 page, and more  
✔ **Composer-Friendly** – Easily install and manage dependencies  
✔ **Clean Structure** – Intuitive folder organization for better maintainability  

---

## ⚡ Quick Start

### Installation
1. Clone the repository or download the framework:
   ```sh
   git clone https://github.com/rochdi-wafik/CodeMediator.git
   ```
2. Install dependencies (if any) via Composer in `Application/Libraries`
   ```sh
   composer install
   ```
3. Configure your application in `Application/Config/app.php`:
   ```php
   $config['base_url'] = 'http://localhost/your-project';
   $config['devmode'] = true; // false for production
   $config['default_controller'] = HomeController::class;
   ```

### Basic Usage
#### Defining a Route
```php
// config/routes.php
Route::get('about-us', function(){
   Controller::load(PageController::class, 'about';
});
```

#### Creating a Controller
```php
// Application/Controllers/PageController.php
use Core\Classes\Controller;
use Core\Classes\View;

class PageController extends Controller {
    public function about() {
        // Get page data, i.e from DatabaseService
        $page = get_page();
        // Pass page object to view
        View::setData("my_page", $page);
        // Load the view
        View::load('page');
    }
}
```

#### Creating a View
```html
// Application/Views/page.php
use Core\Classes\View;

<main class="container">
    <h1><?= View::getData("my_page")->getTitle() ?></h1>
    <p><?= View::getData("my_page")->getContent() ?></p>
</main>
```


---

## 📖 Full Documentation
Explore the complete documentation to unlock all features:  
👉 **[CodeMediator Documentation](https://github.com/CodeMediator/Documentation)**  

---

## 🤝 Contributing
Contributions are welcome! Feel free to open an **issue** or submit a **pull request**.

## 📜 License
MIT © [Rochdi Wafik](https://github.com/rochdi-wafik)
