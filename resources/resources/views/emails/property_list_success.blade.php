<!DOCTYPE html>
<html>
<head>
    <title>Property Listed Successfully!</title>
</head>
<body>
    <h1>Property Listed Successfully!</h1>
    <p>Admin has listed your property. Here are the details:</p>
    <ul>
        <li><strong>Title:</strong> {{ $property['title'] }}</li>
        <li><strong>Description:</strong> {{ $property['description'] }}</li>
        <li><strong>Price:</strong> {{ $property['price'] }}</li>
        <li><strong>Location:</strong> {{ $property['location'] }}</li>
    </ul>
</body>
</html>