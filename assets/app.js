/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/global.scss';

// start the Stimulus application
import './bootstrap';

// le bout de code à ajouter
// alert('Webpack fonctionne !')

const $ = require('jquery');
require('bootstrap');

import 'select2';
$(document).ready(function() { // permet de lancer Select2 sur tous les élements avec la classe 'select2'
    $('select').select2()
});