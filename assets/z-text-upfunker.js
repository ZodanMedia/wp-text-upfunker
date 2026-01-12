/**
 * Plugin Name: Zodan Text Upfunker
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
class zodanTextUpfunkerSingle {
	constructor(el, effect, maxLoops, charspeed, wordspeed, cyclespeed) {
		'use strict';

        this.el = el;
        this.effect = effect || 'code';
        this.maxLoops = maxLoops || Infinity;
        this.charspeed = charspeed || 100;
        this.wordspeed = wordspeed || 350;
        this.cyclespeed = cyclespeed || 500;
        // console.log(this.maxLoops);
        this.timeOuts = {
            codes: 20,  // 20ms between code animations
            chars: this.charspeed, // 100ms between characters
            words: this.wordspeed, // 100ms between characters
            cycles: this.cyclespeed  // timeout between cycles;
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
			return;
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
			this.effect = this.randomObjectKey(this.effects);
		} else {
			this.effect = 'code'; // Default effect
		}
		// console.log(this.effect);
		this.effectType = this.effects[this.effect];
		this.charEffectClass = this.charEffectsClasses[this.effect];

		// get words from the element and clean up original text
		this.getWordsFromElement();
		this.el.innerHTML = '';

		this.message = 0;
		this.current_length = 0;
		this.animateWordsBuffer = false;

		this.currentChild = false;
		
		// create the first item (for code only)
		if( this.effect == 'code' ) {
			this.currentChild = document.createElement('span');
			this.el.append(this.currentChild);
		}

		// Create an observer
		const observer = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				// When entering the viewport
				if (entry.isIntersecting) {
					// Fire custom event
					const event = new CustomEvent('zUpFunkerAnimationStarted', {
						detail: {
							message: 'Upfunker started',
							element: this.el
						}
					});
					this.el.dispatchEvent(event);

					// Start animation
					setTimeout(this.effectType, this.timeOuts.cycles);

					// Stop observing
					observer.unobserve(entry.target);
				}
			});
		}, {
			threshold: 0.5 // 50% visible
		});
		observer.observe(this.el);


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
		console.log('Start code animation');
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
			for (var i = 0; i < this.messages[this.message].text.length; i++) {
				this.animateWordsBuffer.push({ c: (Math.floor(Math.random() * 12)) + 1, l: this.messages[this.message].text.charAt(i) });
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
			const event = new CustomEvent('zUpFunkerAnimationEnded', {
				detail: { message: 'UpFunker completed all animations', element: this.el }
			});
			this.el.dispatchEvent(event);
			// console.log(this.el);
			return;
		}

		if (this.currentLoop == 1) {
			this.setHeight();
		}

		let messageIndex = 0;
		this.el.innerHTML = '';

		let showNextWord = () => {
			if (messageIndex >= this.messages.length) {
				this.currentLoop++;
				setTimeout(this.loopCharAnimationWords.bind(this), this.timeOuts.cycles);
				return;
			}

			const msg = this.messages[messageIndex];
			let wordSpan;

			// Als er een tag of class-informatie is, gebruik die
			if (msg.tag) {
				wordSpan = document.createElement(msg.tag);
				msg.classList.forEach(cls => wordSpan.classList.add(cls));
				if (msg.style) wordSpan.setAttribute('style', msg.style);
			} else {
				wordSpan = document.createElement('span');
			}

			// altijd de algemene "word" class toevoegen
			wordSpan.classList.add('word', 'word-' + messageIndex);
			this.el.append(wordSpan);

			const chars = Array.from(msg.text);
			let charIndex = 0;

			const display = setInterval(() => {
				const charEl = document.createElement('span');
				charEl.classList.add(this.charEffectClass);
				charEl.textContent = chars[charIndex];
				wordSpan.append(charEl);

				charIndex++;
				if (charIndex >= chars.length) {
					clearInterval(display);
					messageIndex++;
					setTimeout(showNextWord, this.timeOuts.wordspeed);
				}
			}, this.timeOuts.chars);
		};

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

				// Fire custom event
				const event = new CustomEvent('zUpFunkerAnimationEnded', {
					detail: {
						message: 'UpFunker completed all animations',
						element: this.el
					}
				});
				this.el.dispatchEvent(event);

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



	getWordsFromElement() {
		const collectWords = (node, inherited = {}) => {
			Array.from(node.childNodes).forEach(child => {
				if (child.nodeType === Node.TEXT_NODE) {
					const text = child.textContent.trim();
					if (!text) return;
					text.split(/\s+/).forEach(word => {
						if (!word) return;
						this.messages.push({
							text: word,
							tag: inherited.tag || null,
							classList: inherited.classList || [],
							style: inherited.style || ''
						});
					});
				} else if (child.nodeType === Node.ELEMENT_NODE) {
					// Lees attributen over
					const inheritedAttrs = {
						tag: child.tagName.toLowerCase(),
						classList: Array.from(child.classList),
						style: child.getAttribute('style') || ''
					};
					collectWords(child, inheritedAttrs);
				}
			});
		};

		this.messages = [];
		collectWords(this.el);
		this.el.innerHTML = '';
	}



	// method to get chars from text
	splitTextToChars(text) {
		// Create a temporary DOM element to decode HTML entities
		// var el = document.createElement('textarea');
		// el.innerHTML = text;
		// let decodedText = el.value;
		// // Split string into array of characters
		// return decodedText.split('');

    	return Array.from(text);

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
		// console.log('resized');
		this.el.setAttribute('style','');
		this.currentLoop = 0
	}

}

class zodanTextUpfunker {
	constructor( options ) {
		const selector = options.elem || '';
		const effect = options.type || 'code';
		const maxLoops = options.cycles || 0;
		const charspeed = options.charspeed || 100;
		const wordspeed = options.wordspeed || 350;
		const cyclespeed = options.cyclespeed || 500;

		// console.log(options);
		const elements = document.querySelectorAll(selector);

		if (!elements.length) {
			// console.warn(`[zTextUpfunker] No elements found for selector: "${selector}"`);
			return;
		}

		// Create instances for all elements
		elements.forEach(el => {
			if (el instanceof HTMLElement) {
				new zodanTextUpfunkerSingle(el, effect, maxLoops, charspeed, wordspeed, cyclespeed);
			}
		});

	}
}


// Let's funk!
if( zodanTextUpfunkerParams ) {
	zodanTextUpfunkerParams.items.forEach( item => {
		new zodanTextUpfunker( item );
	});
}