import { store, getContext } from '@wordpress/interactivity';
store( 'wugweb/marquee', {
	state: {
		get label() {
			return getContext().paused ? 'Play' : 'Pause';
		},
	},
	actions: {
		toggle() {
			const ctx = getContext();
			ctx.paused = ! ctx.paused;
		},
	},
} );
