<!doctype html>
<html lang="en"><head><meta charset="utf-8"><title>New inquiry</title></head><body>
<h1>New WebWorksLab inquiry</h1>
<p><strong>Name:</strong> {{ $inquiry['name'] }}</p>
<p><strong>Email:</strong> {{ $inquiry['email'] }}</p>
<p><strong>Phone:</strong> {{ $inquiry['phone'] ?? 'Not provided' }}</p>
<p><strong>Website:</strong> {{ $inquiry['website'] ?? 'Not provided' }}</p>
<h2>Message</h2>
<p style="white-space: pre-wrap">{{ $inquiry['message'] }}</p>
</body></html>
