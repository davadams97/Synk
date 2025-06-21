## What is  Synk?
Synk is a web-based application designed to simplify the 
process of transferring playlists between different music 
providers. 

Many users hesitate to switch to a new music 
provider, even if it offers better services, due to the 
effort invested in curating and personalizing playlists 
over months or even years. With Synk, that concern is now 
a thing of the past. 

Let Synk take care of seamlessly 
transferring your playlists for you!

## Running the Servers

### Synk (Laravel Application)
```bash
cd projects/synk
composer install
npm install
php artisan serve
npm run dev
```

### YTMusicProxy (Flask API)
```bash
cd projects/ytmusicproxy
pip install -r requirements.txt
flask run
```

## Inspiration
I wanted to build and deploy an application with the technology
stack that I am currently using. I wanted to build an application 
from scratch and learn about the intricacies of Laravel. 

## Technical Stack 
- Laravel 
- Tailwind
- Inertia.js
- Redis 
- Flask
- Vue.js
