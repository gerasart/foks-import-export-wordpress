<script>
 fetch('http://wp:8888/wp-content/plugins/foksImportExport/logs/products.json').then(response => response.json())
     .then(data => console.log(data))
     .catch(err => console.error(err));
</script>
