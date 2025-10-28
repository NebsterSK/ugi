const images = import.meta.glob('../images/*.{png,jpg,jpeg,gif,svg}', {
    eager: true,
    import: 'default',
});

export default Object.values(images);