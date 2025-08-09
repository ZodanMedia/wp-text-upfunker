/**
 * Plugin Name: Z Text Upfunker
 * Javascript
 * 
 * Author: Zodan
 * Author URI: https://zodan.nl
 */



/*
 * Simple funky text appearances 
 * 
 * Animates the appearance of words, with different effects
 *
 * @params string parentSelector
 * @params string effect
 * @params integer maxLoops
 */
class zTextUpfunkerSingle {
	constructor(el, effect, maxLoops) {
		'use strict';

        this.el = el;
        this.effect = effect || 'code';
        this.maxLoops = maxLoops || Infinity;
        console.log(this.maxLoops);
        this.timeOuts = {
            codes: 20,  // 20ms between code animations
            chars: 100, // 100ms between characters
            cycles: 500  // timeout between cycles;
        };
		this.codecharacters = "&µ#*+%8!¢?£1@§$"; // chars used for code animation
		this.charEffectsClasses = {
			code: 'charCode',
			fade: 'charFadeIn',
			flip: 'charFlip',
			sink: 'charSink',
			pop: 'charPop',
			flkr: 'charFlkr',
			circ: 'charCirc'
		}
		this.effects = {};

		this.currentLoop = 0; // Teller voor de huidige loop
		this.messages = [];
		this.message = 0;

		// initiate Class
		this.init();

	}
		init() {
			// If the user prefers reduced motion, then bail out
			const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
			if (prefersReducedMotion.matches) {
				// Toon de tekst zonder animatie
				this.el.innerHTML = this.messages.join(' ');
				return; // Stop de verdere animatie
			}
			// Trying to ensure some styling
			this.el.classList.add('zMessageUpfunkerParent', 'zmupActive');
			this.setHeight();

			this.effects = {
				code: this.startCodeAnimation.bind(this),
				fade: this.startCharAnimation.bind(this),
				flip: this.startCharAnimation.bind(this),
				sink: this.startCharAnimation.bind(this),
				pop: this.startCharAnimation.bind(this),
				flkr: this.startCharAnimation.bind(this),
				circ: this.startCharAnimation.bind(this)
			};

			if (this.effect && this.effect in this.effects) {
				// all well, do nothing

			} else if (this.effect === 'rand') {
				// let eff_keys = Object.keys(this.effects);
				// let eff_rnd = Math.floor(Math.random() * eff_keys.length);
				// effect = eff_keys[eff_rnd];
				this.effect = this.randomObjectKey(this.effects);
			} else {
				this.effect = 'code'; // Default effect
			}
			console.log(this.effect);
			this.effectType = this.effects[this.effect];
			this.charEffectClass = this.charEffectsClasses[this.effect];

			this.getWordsFromElement();

			this.message = 0;
			this.current_length = 0;
			this.animateWordsBuffer = false;

			this.currentChild = false;
			
			// create the first item (for code only)
			if( this.effect == 'code' ) {
				this.currentChild = document.createElement('span');
				this.el.append(this.currentChild);
			}

			// start the animation
			setTimeout(this.effectType, this.timeOuts.cycles);

			// on resize: reset loop and clear element styles
			window.addEventListener('resize', () => {
				this.resetHeight();
			});

		}


		/*
		 * 1)  code
		 * - The effect lets words appear from code glyphs
		 *   morphing into the actual characters
		 *
		 * ***************************************************** */
		// method to start animating code glyphs
		startCodeAnimation() {

			if (this.current_length < this.messages[this.message].length) {
				this.current_length = this.current_length + 2;
				if (this.current_length > this.messages[this.message].length) {
					this.current_length = this.messages[this.message].length;
				}
				var message = this.generateRandomString(this.current_length);
				this.currentChild.innerHTML = message;
				setTimeout(this.startCodeAnimation.bind(this), this.timeOuts.codes);
			} else {
				setTimeout(this.codeAnimateWords.bind(this), this.timeOuts.codes);
			}
		}

		// method to blend in the actual words
		codeAnimateWords() {

			if (this.animateWordsBuffer === false) {
				this.animateWordsBuffer = [];
				for (var i = 0; i < this.messages[this.message].length; i++) {
					this.animateWordsBuffer.push({ c: (Math.floor(Math.random() * 12)) + 1, l: this.messages[this.message].charAt(i) });
				}
			}
			var do_cycles = false;
			var message = '';

			for (var i = 0; i < (this.animateWordsBuffer.length); i++) {
				var fader = this.animateWordsBuffer[i];
				if (fader.c > 0) {
					do_cycles = true;
					fader.c--;
					message += this.codecharacters.charAt(Math.floor(Math.random() * this.codecharacters.length));
				} else {
					message += fader.l;
				}
			}
			this.currentChild.innerHTML = message;

			if (do_cycles === true) {
				setTimeout(this.codeAnimateWords.bind(this), 50);
			} else {
				setTimeout(this.cycleCodeText.bind(this), this.timeOuts.cycles);
			}
		}



		/*
		 * 2)  Fade and Flip and other effects
		 * - The effect lets words appear by fading in each character or after
		 *   flipping with rotateY
		 *
		 * Maybe not in promises, but more simple like
		 * https://stackoverflow.com/questions/34370622/javascript-how-do-i-display-a-text-character-by-character
		 *
		 * ***************************************************** */
		// method to start animating fade-in
		startCharAnimation() {
			this.loopCharAnimationWords(); // Start the fade animation loop
		}

		loopCharAnimationWords() {
			if (this.currentLoop >= this.maxLoops) {
				return; // Stop the animation if the maximum number of loops is reached
			}
			if (this.currentLoop == 1) {
				this.setHeight();
			}
		
			// Loop door alle woorden in this.messages
			let messageIndex = 0;
			this.el.innerHTML = '';
		
			// Functie om per woord de karakters te tonen
			let showNextWord = () => {
				if (messageIndex >= this.messages.length) {
					this.currentLoop++; // Verhoog de loop teller aan het einde van de cyclus
					setTimeout(this.loopCharAnimationWords.bind(this), this.timeOuts.cycles);
					return; // Einde van de berichten, stoppen.
				}
			
				let wordSpan = document.createElement('span');
				wordSpan.classList.add('word');
				wordSpan.classList.add('word-' + messageIndex);
				this.el.append(wordSpan);
			
				let chars = this.splitTextToChars(this.messages[messageIndex]);
			
				let charIndex = 0;
			
				// Toon de karakters één voor één binnen het huidige woord
				let display = setInterval(() => {  // Gebruik een arrow function hier
					let charEl = document.createElement('span');
					charEl.classList.add(this.charEffectClass);
					charEl.innerHTML = chars[charIndex];
					wordSpan.append(charEl);
			
					charIndex++;
					if (charIndex >= chars.length) {
						clearInterval(display); // Stop de interval zodra het hele woord is getoond
						messageIndex++;
						setTimeout(showNextWord, 500); // Wacht een halve seconde voordat het volgende woord start
					}
				}, this.timeOuts.chars); // Toon elke 100ms een karakter
			};
		
			// Start met het eerste woord
			showNextWord();
		}




		/*
		 * Helper functions
		 *
		 * ***************************************************** */
		// method to cycle through this.messages and create spans
		cycleCodeText() {
			this.message = this.message + 1;
			if (this.message >= this.messages.length) {
				this.currentLoop++;
				if( this.currentLoop >= this.maxLoops ) {
					return;
				} else {
					this.message = 0;
					this.el.innerHTML = '';
				}
			}
			this.currentChild = document.createElement('span');
			this.el.append(this.currentChild);

			this.current_length = 0;
			this.animateWordsBuffer = false;
			this.currentChild.innerHTML = '';

			setTimeout(this.effectType, this.timeOuts.cycles);

		}

		// method to get words from the selector's content
		getWordsFromElement() {
			// get the content of the first child node
			let elFirstContent = this.el.innerHTML.trim();
			// split by m dash of n dashes or comma
			let allElements = Array.from(elFirstContent.split(/[–, -]/g));
			// trim all elements
			let allElementsTrimmed = allElements.map(string => string.trim());
			// remove empty elements
			let allElementsFiltered = allElementsTrimmed.filter(el => el.length > 0);

			this.messages = allElementsFiltered;

			console.log(this.messages);
			// empty the parent element
			this.el.innerHTML = '';
		}


		// method to get chars from text
		splitTextToChars(text) {
			// Create a temporary DOM element to decode HTML entities
			var el = document.createElement('textarea');
			el.innerHTML = text;
			let decodedText = el.value;
			// Split string into array of characters
			return decodedText.split('');
		}

		// method to generate a random string from this.codecharacters
		generateRandomString(length) {
			var random_text = '';
			while (random_text.length < length) {
				random_text += this.codecharacters.charAt(Math.floor(Math.random() * this.codecharacters.length));
			}
			return random_text;
		}
		// method to return a random key of an object
		randomObjectKey(object) {
			  let eff_keys = Object.keys(object);
			  let eff_rnd = Math.floor(Math.random() * eff_keys.length);
			return eff_keys[eff_rnd];
		}
		// method to return a random prop of an object
		randomObjectProp( object ) {
			let keys = Object.keys(object);
			return object[keys[ keys.length * Math.random() << 0]];
		}
		setHeight(){
			let upfunkContainerHeight = this.el.clientHeight;
			this.el.setAttribute('style','height:'+upfunkContainerHeight+'px');
		}
		resetHeight(){
			console.log('resized');
			this.el.setAttribute('style','');
			this.currentLoop = 0
		}
		

}

class zTextUpfunker {
	constructor( options ) {
		const selector = options.elem || '';
		const effect = options.type || 'code';
		const maxLoops = options.cycles || 0;

		console.log(options);
		const elements = document.querySelectorAll(selector);

		if (!elements.length) {
			console.warn(`[zTextUpfunker] No elements found for selector: "${selector}"`);
			return;
		}
		elements.forEach(el => {
			if (el instanceof HTMLElement) {
				new zTextUpfunkerSingle(el, effect, maxLoops);
			}
		});
	}
}

// Let's funk!
if( zTextUpfunkerParams ) {
	zTextUpfunkerParams.items.forEach( item => {
		new zTextUpfunker( item );
	});
}