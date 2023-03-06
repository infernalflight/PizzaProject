import { Controller } from 'stimulus';
import $ from 'jquery'

export default class extends Controller {
    connect() {
        $('#download_pizza').on('click', function() {
           alert('Désolé votre imprimante à Pizza est en panne. Veuillez venir la chercher directement');
        });
    }
}