<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirNote | An online clipboard to share notes!</title>
    <meta name="description" content="An online clipboard to share notes!">
    <meta name="keywords" content="Clipboard, Online, Note, Share">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <link rel="icon" href="favicon.png">
    <link rel="apple-touch-icon" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        textarea {
            height: 40vh;
        }
    </style>
</head>
<body>

<main class="container mt-5 text-center">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h1 class="display-5">AirNote</h1>
            <p class="lead">An online clipboard to share notes!</p>

            <ul class="nav nav-tabs mt-5" id="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="share-tab" data-bs-toggle="tab" data-bs-target="#share"
                            type="button" role="tab" aria-controls="share" aria-selected="true">Share
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link" id="receive-tab" data-bs-toggle="tab" data-bs-target="#receive"
                            type="button" role="tab" aria-controls="receive" aria-selected="false">Receive
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="tabsContent">
                <div class="tab-pane fade show active" id="share" role="tabpanel" aria-labelledby="share-tab">
                    <div class="card bg-light text-left border-top-0 rounded-0 rounded-bottom">
                        <div class="card-body d-flex flex-column gap-2">
                            <textarea class="form-control" title="Note" id="share-note"
                                      placeholder="Enter your note here and press Share button."></textarea>
                            <input class="btn btn-dark" type="button" value="Share" id="share-btn" disabled>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="receive" role="tabpanel" aria-labelledby="receive-tab">
                    <div class="card bg-light text-left border-top-0 rounded-0 rounded-bottom">
                        <div class="card-body d-flex flex-column gap-2">
                            <input type="text" id="receive-id" placeholder="Note ID" title="Note ID"
                                   class="form-control" style="text-transform: uppercase">
                            <textarea class="form-control text-start" title="Note" id="receive-note"
                                      readonly>Enter the note id to receive the note content...</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-5 text-muted small">
                &copy; <?php echo date('Y') ?> by
                <a href="https://miladrahimi.com" title="Milad Rahimi">Milad Rahimi</a> |
                <a href="https://github.com/miladrahimi/airnote" title="GitHub Repository">GitHub</a>
            </p>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        // Tabs
        let triggerTabList = [].slice.call(document.querySelectorAll('#tabs a'))
        triggerTabList.forEach(function (triggerEl) {
            let tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })

        // Share (Note)
        $('#share-note').bind('input', function () {
            $('#share-btn').attr('disabled',
                $(this).val() === '' ||
                $(this).val().startsWith('[RESPONSE]')
            ).val('Share')
        })

        // Share (Button)
        $('#share-btn').click(function () {
            let me = $(this)
            let note = $('#share-note')

            me.val('Processing...').prop('disabled', true)
            note.prop('disabled', true)

            let request = $.ajax({url: 'process.php', type: 'post', data: {'note': note.val()}})

            request.done(function (response) {
                note.val([
                    '[RESPONSE]',
                    'NOTE ID: ' + response['id'].toUpperCase(),
                    'NOTE URL: ' + response['url'],
                ].join("\n"))
                me.val('Shared :)')
                setTimeout(function () {
                    me.val('Share').prop('disabled', true)
                    note.prop('disabled', false)
                }, 2000)
            })

            request.fail(function (jqXHR, textStatus, errorThrown) {
                console.error(jqXHR, textStatus, errorThrown)
                me.val('Failed :(')
                setTimeout(function () {
                    me.val('Share').prop('disabled', false)
                    note.prop('disabled', false)
                }, 2000)
            })
        })

        // Receive
        $('#receive-id').keyup(function () {
            let id = $(this).val()
            let note = $('#receive-note')
            note.val('Receiving...')

            let request = $.ajax({url: 'process.php', type: 'get', data: {'id': id}})

            request.done(function (response) {
                note.val(response['note'])
            })

            request.fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 404) {
                    note.val('ERROR: Note not found :(')
                } else {
                    console.error(jqXHR, textStatus, errorThrown)
                    note.val('ERROR: Failed to receive the note :(')
                }
            })
        })
    })
</script>

</body>
</html>
