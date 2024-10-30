import './hyakuninisshu.scss';

import apiFetch from '@wordpress/api-fetch';

import {
	render,
	useState,
	useEffect
} from '@wordpress/element';

const HyakuninIsshu = () => {

	const interval = parseInt( hyakuninisshu_data.interval_sec ) * 1000;

	const [ currentTanka, updatecurrentTanka ] = useState( hyakuninisshu_data.tanka );
	const [ currentAuthor, updatecurrentAuthor ] = useState( hyakuninisshu_data.author );
	const [ currentSource, updatecurrentSource ] = useState( hyakuninisshu_data.source );
	const [ currentSubject, updatecurrentSubject ] = useState( hyakuninisshu_data.subject );

	useEffect( () => {
		let timer = setInterval( () => {
			apiFetch( {
				path: 'rf/hyakunin_isshu_api/token',
			} ).then( ( response ) => {
				//console.log( response );
				updatecurrentTanka( response['tanka'] );
				updatecurrentAuthor( response['author'] );
				updatecurrentSource( response['source'] );
				updatecurrentSubject( response['subject'] );
			} );
		}, interval );
		return () => {
			clearInterval( timer );
		};
	}, [ currentTanka, currentAuthor, currentSource, currentSubject ] );

	const items = [];
	items.push(
		<div className="hy_tooltip">
			<div>{ currentTanka }</div>
			<span className="hy_tooltiptext">
				{ hyakuninisshu_data.author_label } : { currentAuthor } / { hyakuninisshu_data.source_label } : { currentSource } / { hyakuninisshu_data.subject_label } : { currentSubject }
			</span>
		</div>
	);

	return (
		<>
			{ items }
		</>
	);

};

render(
	<HyakuninIsshu />,
	document.getElementById( 'hyakuninisshu' )
);

