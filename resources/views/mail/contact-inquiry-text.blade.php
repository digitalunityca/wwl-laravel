New WebWorksLab inquiry

Name: {{ $inquiry['name'] }}
Email: {{ $inquiry['email'] }}
Phone: {{ $inquiry['phone'] ?? 'Not provided' }}
Website: {{ $inquiry['website'] ?? 'Not provided' }}

Message:
{{ $inquiry['message'] }}
