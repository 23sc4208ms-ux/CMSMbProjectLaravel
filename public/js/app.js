$(function () {
    const apiBase = '/ajax/students';
    const degreesUrl = '/degrees.json';
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getStudentName(student) {
        return [student.last_name, student.first_name, student.middle_name]
            .filter(function (part) {
                return part && String(part).trim() !== '';
            })
            .join(' ');
    }

    function showMessage(text, type = 'success') {
        const message = $('<div>').addClass('message').addClass(type === 'success' ? 'success' : 'error').text(text);
        $('#messages').empty().append(message);
        setTimeout(function () {
            message.fadeOut(400, function () {
                message.remove();
            });
        }, 4000);
    }

    function showErrors(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            const firstKey = Object.keys(errors)[0];
            const firstError = firstKey ? errors[firstKey][0] : 'Validation failed.';
            showMessage(firstError, 'error');
            return;
        }

        if (xhr.responseJSON && xhr.responseJSON.error) {
            showMessage(xhr.responseJSON.error, 'error');
            return;
        }

        showMessage('Request failed. Please try again.', 'error');
    }

    function openModal(title) {
        $('#modalTitle').text(title);
        $('#modal').css('display', 'flex');
    }

    function closeModal() {
        $('#studentForm')[0].reset();
        $('#student_id_field').val('');
        $('#modal').hide();
    }

    function renderDegrees(degrees) {
        const options = ['<option value="">Choose degree</option>'];

        degrees.forEach(function (degree) {
            const label = degree.code || degree.name || `Degree ${degree.id}`;
            options.push(`<option value="${escapeHtml(degree.id)}">${escapeHtml(label)}</option>`);
        });

        $('#degree_id').html(options.join(''));
    }

    function loadDegrees() {
        return $.getJSON(degreesUrl)
            .done(function (data) {
                renderDegrees(Array.isArray(data) ? data : []);
            })
            .fail(function () {
                $('#degree_id').html('<option value="">Unable to load degrees</option>');
            });
    }

    function renderStudents(students) {
        if (!students.length) {
            $('#studentsTable tbody').html('<tr><td colspan="6">No students found.</td></tr>');
            return;
        }

        const rows = students.map(function (student, index) {
            const degreeLabel = student.degree ? (student.degree.code || student.degree.name || '') : '';

            return `
                <tr data-id="${escapeHtml(student.id)}">
                    <td>${index + 1}</td>
                    <td>${escapeHtml(student.student_id)}</td>
                    <td>${escapeHtml(getStudentName(student))}</td>
                    <td>${escapeHtml(student.email)}</td>
                    <td>${escapeHtml(degreeLabel)}</td>
                    <td>
                        <button type="button" class="btn edit-student">Edit</button>
                        <button type="button" class="btn danger delete-student">Delete</button>
                    </td>
                </tr>
            `;
        }).join('');

        $('#studentsTable tbody').html(rows);
    }

    function loadStudents() {
        return $.getJSON(apiBase)
            .done(function (data) {
                renderStudents(Array.isArray(data.students) ? data.students : []);
            })
            .fail(function () {
                showMessage('Failed to load students.', 'error');
            });
    }

    function getStudentById(id) {
        return $.getJSON(`${apiBase}/${id}`);
    }

    $('#btnAdd').on('click', function () {
        closeModal();
        openModal('Add Student');
    });

    $('#btnCancel').on('click', function () {
        closeModal();
    });

    $('#studentsTable').on('click', '.edit-student', function () {
        const studentId = $(this).closest('tr').data('id');

        getStudentById(studentId)
            .done(function (response) {
                const student = response.student;

                $('#student_id_field').val(student.id);
                $('#student_id').val(student.student_id);
                $('#first_name').val(student.first_name);
                $('#middle_name').val(student.middle_name || '');
                $('#last_name').val(student.last_name);
                $('#email').val(student.email);
                $('#degree_id').val(student.degree_id || '');
                $('#address').val(student.address || '');
                $('#contact_number').val(student.contact_number || '');
                $('#password').val('');

                openModal('Edit Student');
            })
            .fail(function () {
                showMessage('Unable to load the selected student.', 'error');
            });
    });

    $('#studentsTable').on('click', '.delete-student', function () {
        const studentId = $(this).closest('tr').data('id');

        if (!confirm('Delete this student?')) {
            return;
        }

        $.ajax({
            url: `${apiBase}/${studentId}`,
            method: 'DELETE',
        })
            .done(function () {
                showMessage('Student deleted successfully.');
                loadStudents();
            })
            .fail(showErrors);
    });

    $('#studentForm').on('submit', function (event) {
        event.preventDefault();

        const studentId = $('#student_id_field').val();
        const payload = {
            student_id: $('#student_id').val(),
            first_name: $('#first_name').val(),
            middle_name: $('#middle_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            degree_id: $('#degree_id').val(),
            address: $('#address').val(),
            contact_number: $('#contact_number').val(),
            password: $('#password').val(),
        };

        const request = studentId
            ? $.ajax({
                url: `${apiBase}/${studentId}`,
                method: 'PUT',
                data: payload,
            })
            : $.ajax({
                url: apiBase,
                method: 'POST',
                data: payload,
            });

        request
            .done(function () {
                showMessage(studentId ? 'Student updated successfully.' : 'Student created successfully.');
                closeModal();
                loadStudents();
            })
            .fail(showErrors);
    });

    $('#modal').on('click', function (event) {
        if (event.target === this) {
            closeModal();
        }
    });

    loadDegrees();
    loadStudents();
});
