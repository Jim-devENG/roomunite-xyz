<!DOCTYPE html>
<html>
<head>
    <title>New Property Created</title>
</head>
<body>
    <h1>New Property Created</h1>
    <p>Hello Admin, a new property has been created. Here are the details:</p>
    <ul>
        <li><strong>Title:</strong> {{ $property['title'] }}</li>
        <li><strong>Description:</strong> {{ $property['description'] }}</li>
        <li><strong>Price:</strong> {{ $property['price'] }}</li>
        <li><strong>Location:</strong> {{ $property['location'] }}</li>
    </ul>
</body>
</html>