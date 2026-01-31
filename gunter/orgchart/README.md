📊 phpBB Organization Chart Extension
A phpBB extension that adds organizational chart functionality to your forum, allowing administrators to display team structure and hierarchy visually.

✨ Features

🎯 Visual organization chart display  
👥 Display forum staff hierarchy  
🎨 Customizable design and layout  
📱 Responsive design for all devices  
⚙️ Easy-to-use ACP (Administration Control Panel) interface  
🔄 Automatic synchronization with user groups and ranks  
🎨 Multiple chart styles and themes  
📊 Interactive chart navigation  

# 📋 Requirements

phpBB 3.2.x or 3.3.x  
PHP 7.1.3 or higher  
Modern web browser with JavaScript enabled  

# 🚀 Installation
Via phpBB Extension Database (Recommended)  

Download the latest release from the phpBB Extension Database  
Extract the archive  
Upload the orgchart folder to phpBB/ext/gunter/  
Navigate to ACP → Customise → Manage extensions  
Enable the "Organization Chart" extension  

# Manual Installation
```bash
cd phpBB/ext/
mkdir -p gunter/orgchart
cd gunter/orgchart
```
Upload extension files here  
Then activate via ACP → Customise → Manage extensions  

# ⚙️ Configuration
Navigate to ACP → Extensions → Organization Chart  
Configure settings:  

Select which user groups to include  
Set custom titles and positions  
Save settings  

# 📖 Usage
For Administrators  

Manage the organization chart through the ACP  
Add/remove positions and assign users  
Customize appearance and hierarchy  
Set visibility permissions  

For Users

View the organization chart at /orgchart or via forum menu  
See team structure and contact information  
Navigate through different departments/teams  

# 🎨 Customization
Templates  
Edit the templates in styles/all/template/ to customize the appearance:  

orgchart_body.html - Main chart display  

Styling
Customize CSS in styles/all/theme/orgchart_body.css  
Language Files  
Add translations in language/[lang_code]/orgchart.php  

# 🔧 Development
Building from Source  
```bash
git clone https://github.com/guntergbr/lspd-orgchart-phpbb.git
cd phpbb-orgchart
composer install
npm install
npm run build
```
Running Tests
```bash
gunter/bin/phpunit
```
🤝 Contributing
Contributions are welcome! Please follow these steps:  

Fork the repository  
Create a feature branch (git checkout -b feature/AmazingFeature)  
Commit your changes (git commit -m 'Add some AmazingFeature')  
Push to the branch (git push origin feature/AmazingFeature)  
Open a Pull Request  

# Coding Standards

Follow phpBB coding guidelines  
Include PHPDoc comments  
Write tests for new features  
Update documentation  

# 🐛 Bug Reports
Found a bug? Please open an issue with:  

phpBB version  
PHP version  
Extension version  
Steps to reproduce  
Expected vs actual behavior  
Screenshots (if applicable)  

# 📝 Changelog
Version 1.0.0 (2025-01-31)  

Initial release  
Basic organization chart functionality  
ACP configuration panel  
Responsive design  

See CHANGELOG.md for full version history.  

# 📄 License
This extension is released under the GNU General Public License v2  
# 👤 Author
guntergbr  

GitHub: @guntergbr  

# 🙏 Credits

phpBB Extension Development Team  
D3.js for chart visualization  
Contributors and testers  

# 📞 Support

GitHub Issues
phpBB Extension Forum
Documentation

⭐ If you find this extension useful, please consider giving it a star on GitHub!
