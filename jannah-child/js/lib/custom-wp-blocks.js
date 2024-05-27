

function changeHeadingBlockTitle( settings, name ) {
    if ( name !== 'core/verse' ) {
        return settings;
    }

    return lodash.assign( {}, settings, {
        title: 'Interview',
    } );
}

wp.hooks.addFilter(
    'blocks.registerBlockType',
    'my-plugin/foo',
    changeHeadingBlockTitle
);

const MONGABAY_VARIATION = 'my-plugin/byline-related-posts';

wp.domReady(function() {
    setTimeout(() => {
        const editedPostByline = wp.data.select('core/editor').getEditedPostAttribute('byline');

        console.log('Términos de la taxonomía "byline":', editedPostByline);

        if (editedPostByline && editedPostByline.length > 0) {
            
        	const bylineTermIds = editedPostByline.map(term => term.id);
            console.log('IDs de los términos de la taxonomía "byline":', bylineTermIds);

            const queryArgs = {
                perPage: 5,
                offset: 0,
                order: 'desc',
                orderBy: 'date',
                taxQuery: [
                    {
                        taxonomy: 'byline',
                        field: 'id',
                        terms: editedPostByline,
                    },
                ],
            };

            console.log('Argumentos de la consulta:', queryArgs);

            wp.apiFetch({
                path: '/wp/v2/posts',
                method: 'GET',
                queryArgs,
            }).then(posts => {
                console.log('Posts relacionados:', posts);

            wp.blocks.registerBlockVariation('core/query', {
                name: MONGABAY_VARIATION,
                title: 'Related Stories',
                description: 'Displays a list of posts related to the current byline terms',
                isActive: ({ namespace }) => namespace === MONGABAY_VARIATION,
                icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2c-5.522 0-10 4.477-10 10s4.478 10 10 10 10-4.477 10-10-4.478-10-10-10zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-.015-14.895c-2.324 0-4.313 1.077-5.68 2.778l1.429 1.429c1.014-1.299 2.532-2.204 4.25-2.204.383 0 .754.053 1.11.153l.946-1.91c-1.06-.47-2.201-.736-3.356-.736zm5.695 3.775l-1.429 1.429c1.269 1.704 1.99 3.763 1.99 5.792 0 .288-.023.57-.057.848l1.71.854c.057-.472.086-.956.086-1.452 0-2.748-.932-5.271-2.505-7.621zm-9.853 7.67l-1.71-.854c-.057.473-.086.957-.086 1.453 0 2.747.932 5.27 2.505 7.62 2.324 0 4.313-1.077 5.68-2.778l-1.429-1.429c-1.015 1.298-2.533 2.204-4.25 2.204-.384 0-.754-.053-1.11-.153l-.946 1.91c1.06.47 2.201.736 3.356.736 2.324 0 4.313-1.077 5.68-2.778l-1.43-1.429c-1.27 1.704-1.991 3.763-1.991 5.792 0 .287.022.57.057.847l1.71.854c.057-.472.086-.957.086-1.452 0-2.748-.932-5.271-2.505-7.621z"/></svg>',
                attributes: {
                    namespace: MONGABAY_VARIATION,
                    query: queryArgs,
                },
                scope: ['inserter'],
            });
            }).catch(error => {
                console.error('Error al recuperar los posts relacionados:', error);
            });
        } else {
            console.log('Los términos de la taxonomía "byline" aún no están disponibles. Esperando...');
        }
    }, 1000);
});

 