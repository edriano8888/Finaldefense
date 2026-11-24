<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipeline - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- React & ReactDOM -->
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <!-- Babel -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            green: '#1FAE5B',
                            deep: '#0F6B3E',
                            dark: '#111827',
                            gray: '#F9FAFB',
                            border: '#E5E7EB',
                            blue: '#3B82F6',
                            purple: '#8B5CF6',
                            rose: '#F43F5E',
                            amber: '#F59E0B'
                        }
                    },
                    boxShadow: {
                        'card': '0 2px 5px -1px rgba(0, 0, 0, 0.05), 0 1px 3px -1px rgba(0, 0, 0, 0.03)',
                        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)',
                        'column': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.02)',
                    }
                }
            }
        }
    </script>
    <!-- CSRF Token -->
    <meta name="csrf-token-name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">
    
    <link href="<?php echo base_url('assets/css/influencer.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <script>
        // Axios CSRF Setup
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]').content;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Add CSRF token to all POST requests
        axios.interceptors.request.use(function (config) {
            if (config.method === 'post') {
                if (config.data instanceof FormData) {
                    config.data.append(csrfTokenName, csrfToken);
                } else {
                    config.data = config.data || {};
                    config.data[csrfTokenName] = csrfToken;
                }
            }
            return config;
        });
    </script>
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
    ?>

    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Influencer', 'child' => 'Pipeline']); ?>

        <!-- Main Content (React Root) -->
        <main class="flex-1 overflow-x-auto bg-[#F8FAFC] p-6 md:p-8 scroll-smooth relative" id="root">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-green-50/50 to-transparent -z-10"></div>
            
            <!-- React will render here -->
            <div class="flex justify-center items-center h-full">
                <div class="flex flex-col items-center">
                     <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-brand-green mb-4"></div>
                     <p class="text-gray-500 text-sm font-medium font-sans">Loading Pipeline...</p>
                </div>
            </div>
        </main>
    </div>

    <!-- React Application -->
    <script type="text/babel">
        const { useState, useEffect } = React;

        const STAGE_CONFIG = {
            'For Outreach': { 
                color: 'bg-yellow-50', 
                badgeColor: 'bg-yellow-100 text-yellow-700',
                borderColor: 'border-yellow-300',
                icon: 'fa-bullhorn'
            },
            'Contacted': { 
                color: 'bg-orange-50', 
                badgeColor: 'bg-orange-100 text-orange-700',
                borderColor: 'border-orange-300',
                icon: 'fa-paper-plane'
            },
            'Replied': { 
                color: 'bg-blue-50', 
                badgeColor: 'bg-blue-100 text-blue-700',
                borderColor: 'border-blue-300',
                icon: 'fa-reply'
            },
            'In - Progress': { 
                color: 'bg-pink-50', 
                badgeColor: 'bg-pink-100 text-pink-700',
                borderColor: 'border-pink-300',
                icon: 'fa-spinner'
            },
            'Not Interested': { 
                color: 'bg-red-50', 
                badgeColor: 'bg-red-100 text-red-700',
                borderColor: 'border-red-300',
                icon: 'fa-times-circle'
            }
        };

        const PlatformIcon = ({ platform, username = '', fullName = '' }) => {
            const combined = ((platform || '') + ' ' + (username || '') + ' ' + (fullName || '')).toLowerCase();
            if (combined.includes('instagram') || combined.includes(' on ig') || combined.includes('_ig')) {
                return <i className="fab fa-instagram text-pink-600"></i>;
            }
            if (combined.includes('youtube') || combined.includes('yt')) {
                return <i className="fab fa-youtube text-red-600"></i>;
            }
            if (combined.includes('facebook') || combined.includes('fb')) {
                return <i className="fab fa-facebook text-blue-600"></i>;
            }
            if (combined.includes('tiktok')) {
                return <i className="fab fa-tiktok text-black"></i>;
            }
            return <i className="fab fa-instagram text-pink-600"></i>;
        };

        const ProfileModal = ({ influencer, onClose, onUpdate }) => {
            const [activeTab, setActiveTab] = useState('basic');
            const [noteContent, setNoteContent] = useState(influencer?.notes || '');
            const [deliverables, setDeliverables] = useState(influencer?.deliverables || '');
            const [fee, setFee] = useState(influencer?.fee || '');
            const [deadline, setDeadline] = useState(influencer?.deadline || '');
            const [editingField, setEditingField] = useState(null);
            const [editValue, setEditValue] = useState('');
            const [saving, setSaving] = useState(false);
            const [showEmailComposer, setShowEmailComposer] = useState(false);
            const [emailSubject, setEmailSubject] = useState('');
            const [emailBody, setEmailBody] = useState('');
            const [emailHistory, setEmailHistory] = useState([]);
            const [sendingEmail, setSendingEmail] = useState(false);
            const [syncingEmail, setSyncingEmail] = useState(false);
            const [tasks, setTasks] = useState([]);
            const [loadingTasks, setLoadingTasks] = useState(false);
            const messagesEndRef = React.useRef(null);
            const [showTemplates, setShowTemplates] = useState(false);

            const fetchTasks = async () => {
                setLoadingTasks(true);
                try {
                    const response = await fetch(`<?= base_url('influencer/get_tasks_ajax/') ?>${influencer.id}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        setTasks(result.data);
                    }
                } catch (error) {
                    console.error('Error fetching tasks:', error);
                }
                setLoadingTasks(false);
            };

            const toggleTaskStatus = async (taskId, currentStatus) => {
                const newStatus = currentStatus === 'Pending' ? 'Done' : 'Pending';
                
                // Optimistic update
                setTasks(prev => prev.map(t => t.id === taskId ? { ...t, status: newStatus } : t));

                const formData = new FormData();
                formData.append('task_id', taskId);
                formData.append('status', newStatus);

                try {
                    const response = await fetch('<?= base_url('influencer/update_task_status_ajax') ?>', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status !== 'success') {
                        // Rollback on error
                        setTasks(prev => prev.map(t => t.id === taskId ? { ...t, status: currentStatus } : t));
                        alert('Failed to update task');
                    }
                } catch (error) {
                    console.error('Error updating task:', error);
                    setTasks(prev => prev.map(t => t.id === taskId ? { ...t, status: currentStatus } : t));
                }
            };

            useEffect(() => {
                if (activeTab === 'tasks') {
                    fetchTasks();
                }
            }, [activeTab]);

            const scrollToBottom = () => {
                messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
            };

            useEffect(() => {
                scrollToBottom();
            }, [emailHistory]);

            const fetchEmailHistory = async () => {
                try {
                    const response = await fetch(`<?= base_url('email/history/') ?>${influencer.id}?t=${new Date().getTime()}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        // Only update if data is different to avoid unnecessary re-renders (simple length check or deep compare if needed)
                        // For now, just set it, React handles diffing
                        setEmailHistory(result.data);
                    }
                } catch (error) {
                    console.error('Error fetching email history:', error);
                }
            };

            // Polling for new emails every 5 seconds
            useEffect(() => {
                let interval;
                if (showEmailComposer) {
                    fetchEmailHistory();
                    interval = setInterval(() => {
                        fetchEmailHistory();
                    }, 5000);
                }
                return () => clearInterval(interval);
            }, [showEmailComposer]);

            const handleSendEmail = async () => {
                if (!emailSubject || !emailBody) {
                    alert('Please fill in subject and body');
                    return;
                }
                setSendingEmail(true);
                const formData = new FormData();
                formData.append('influencer_id', influencer.id);
                formData.append('subject', emailSubject);
                formData.append('body', emailBody);

                try {
                    const response = await fetch('<?= base_url('email/send') ?>', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        // alert('Email sent!'); // Optional feedback
                        setEmailBody('');
                        setEmailSubject(''); // Optionally keep subject for threads
                        fetchEmailHistory();
                        // Don't close modal to allow viewing the sent message immediately
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error sending email:', error);
                    alert('Failed to send email');
                }
                setSendingEmail(false);
            };
            
            const handleSyncEmail = async () => {
                setSyncingEmail(true);
                const formData = new FormData();
                formData.append('influencer_id', influencer.id);
                try {
                    const response = await fetch('<?= base_url('email/sync') ?>', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        fetchEmailHistory();
                    } else {
                        console.error(result.message);
                    }
                } catch (error) {
                    console.error(error);
                }
                setSyncingEmail(false);
            };

            const applyTemplate = (template) => {
                const name = influencer.username || 'there';
                setEmailSubject(template.subject.replace('{{name}}', name));
                setEmailBody(template.body.replace('{{name}}', name));
                setShowTemplates(false);
            };

            useEffect(() => {
                if (showEmailComposer) {
                    fetch(`<?= base_url('email/mark_read/') ?>${influencer.id}`);
                    fetchEmailHistory();
                }
            }, [showEmailComposer]);

            const [toastMessage, setToastMessage] = React.useState('');
            const fileInputRef = React.useRef(null);

            useEffect(() => {
                if (influencer) {
                    setNoteContent(influencer.notes || '');
                    setDeliverables(influencer.deliverables || '');
                    setFee(influencer.fee || '');
                    setDeadline(influencer.deadline || '');
                    setEditingField(null);
                    setEditValue('');
                }
            }, [influencer]);

            if (!influencer) return null;

            const showToast = (msg, type = 'success') => {
                if (typeof window.showToast === 'function') {
                    window.showToast('Pipeline Updated', msg, type);
                } else {
                    setToastMessage(msg);
                    setTimeout(() => setToastMessage(''), 3500);
                }
            };

            const copyToClipboard = (text) => {
                if (!text) return;
                navigator.clipboard.writeText(text);
                showToast("Email copied to clipboard!");
            };

            const formatNumber = (num) => {
                if (num === null || num === undefined || num === '') return '0';
                const val = parseFloat(num);
                if (isNaN(val) || val === 0) return '0';
                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                if (val >= 1000) return (val / 1000).toFixed(1) + 'K';
                return Math.round(val).toLocaleString();
            };

            const handleDeliverablesBlur = async () => {
                if (deliverables !== influencer.deliverables) {
                    setSaving(true);
                    const success = await onUpdate(influencer.id, { deliverables: deliverables });
                    setSaving(false);
                    if (success) showToast("Deliverables updated successfully!");
                }
            };

            const handleFeeBlur = async () => {
                if (fee !== influencer.fee) {
                    setSaving(true);
                    const success = await onUpdate(influencer.id, { fee: fee });
                    setSaving(false);
                    if (success) showToast("Fee updated successfully!");
                }
            };

            const handleDeadlineBlur = async () => {
                if (deadline !== influencer.deadline) {
                    setSaving(true);
                    const success = await onUpdate(influencer.id, { deadline: deadline });
                    setSaving(false);
                    if (success) showToast("Post deadline updated successfully!");
                }
            };

            const handleNoteBlur = async () => {
                if (noteContent !== influencer.notes) {
                    setSaving(true);
                    const success = await onUpdate(influencer.id, { notes: noteContent });
                    setSaving(false);
                    if (success) showToast("Notes saved successfully!");
                }
            };

            const handleStatusChange = async (e) => {
                const newStatus = e.target.value;
                setSaving(true);
                const success = await onUpdate(influencer.id, { pipeline_status: newStatus });
                setSaving(false);
                if (success) showToast(`Pipeline status updated to '${newStatus}'`);
            };

            const handleCampaignTypeChange = async (e) => {
                 const newType = e.target.value;
                 setSaving(true);
                 const success = await onUpdate(influencer.id, { campaign_type: newType });
                 setSaving(false);
                 if (success) showToast(`Campaign type set to '${newType}'`);
            };

            const handleGenerateDiscount = async () => {
                const defaultCode = (influencer.username ? influencer.username.replace(/[^a-zA-Z0-9]/g, '').toUpperCase() : 'INST') + '10';
                const inputCode = prompt("Enter or confirm Discount Code for this creator:", influencer.discount_code || defaultCode);
                if (inputCode !== null && inputCode.trim() !== '') {
                    setSaving(true);
                    const success = await onUpdate(influencer.id, { discount_code: inputCode.trim() });
                    setSaving(false);
                    if (success) showToast(`Discount code '${inputCode.trim()}' saved!`);
                }
            };

            const handleGenerateAffiliate = async () => {
                const defaultLink = `https://instroom.com/aff/${influencer.username || 'creator'}`;
                const inputLink = prompt("Enter or confirm Affiliate Link for this creator:", influencer.affiliate_link || defaultLink);
                if (inputLink !== null && inputLink.trim() !== '') {
                    setSaving(true);
                    const success = await onUpdate(influencer.id, { affiliate_link: inputLink.trim() });
                    setSaving(false);
                    if (success) showToast("Affiliate link saved!");
                }
            };

            const handleAddTag = async () => {
                const newTag = prompt("Enter new tag to add to this creator (e.g. Gift Sent, Active, VIP):", "Gift Sent");
                if (newTag && newTag.trim() !== '') {
                    const currentTags = influencer.niche ? influencer.niche.split(',').map(t => t.trim()).filter(Boolean) : [];
                    if (!currentTags.includes(newTag.trim())) {
                        currentTags.push(newTag.trim());
                        setSaving(true);
                        const success = await onUpdate(influencer.id, { niche: currentTags.join(', ') });
                        setSaving(false);
                        if (success) showToast(`Tag '${newTag.trim()}' added!`);
                    }
                }
            };

            const handleRemoveTag = async (tagToRemove) => {
                const currentTags = influencer.niche ? influencer.niche.split(',').map(t => t.trim()).filter(Boolean) : [];
                const updatedTags = currentTags.filter(t => t !== tagToRemove);
                setSaving(true);
                const success = await onUpdate(influencer.id, { niche: updatedTags.join(', ') });
                setSaving(false);
                if (success) showToast(`Tag '${tagToRemove}' removed!`);
            };

            const handleFileSelected = (e) => {
                const file = e.target.files[0];
                if (file) {
                    showToast(`Attachment '${file.name}' attached to campaign!`);
                }
            };

            const startEditing = (field, value) => {
                setEditingField(field);
                setEditValue(value || '');
            };

            const saveEditing = async () => {
                if (!editingField) return;
                setSaving(true);
                const valToSave = typeof editValue === 'string' ? editValue.trim() : editValue;
                const success = await onUpdate(influencer.id, { [editingField]: valToSave });
                if (success) {
                    setEditingField(null);
                    showToast("Updated successfully!");
                }
                setSaving(false);
            };

            const renderEditableRow = (label, field, value, icon = null, type = 'text') => {
                const isEditing = editingField === field;
                const isEmpty = value === null || value === undefined || value === '';
                return (
                    <div className={`group flex items-start gap-4 py-3 border-b border-gray-100 last:border-0 ${type === 'textarea' ? 'items-start' : 'items-center'}`}>
                        <div className="w-28 flex-shrink-0 text-xs font-bold text-gray-500 uppercase tracking-wide pt-0.5">{label}</div>
                        <div className="flex-1 flex items-center gap-2 min-w-0">
                            {isEditing ? (
                                <div className="flex items-start gap-2 w-full">
                                    {type === 'textarea' ? (
                                        <textarea
                                            value={editValue}
                                            onChange={(e) => setEditValue(e.target.value)}
                                            className="flex-1 border-2 border-brand-green rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20"
                                            rows="3"
                                            autoFocus
                                        ></textarea>
                                    ) : (
                                        <input 
                                            type={type} 
                                            value={editValue} 
                                            onChange={(e) => setEditValue(e.target.value)}
                                            className="flex-1 border-2 border-brand-green rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20"
                                            autoFocus
                                            onKeyDown={(e) => {
                                                if (e.key === 'Enter') saveEditing();
                                                if (e.key === 'Escape') setEditingField(null);
                                            }}
                                        />
                                    )}
                                    <div className="flex gap-1 flex-shrink-0 mt-0.5">
                                        <button onClick={saveEditing} className="w-7 h-7 bg-green-500 hover:bg-green-600 text-white rounded-lg flex items-center justify-center transition-colors">
                                            <i className="fas fa-check text-xs"></i>
                                        </button>
                                        <button onClick={() => setEditingField(null)} className="w-7 h-7 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg flex items-center justify-center transition-colors">
                                            <i className="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-center gap-2 min-w-0 flex-1">
                                    {icon}
                                    <span className={`text-sm truncate ${isEmpty ? 'text-gray-300 italic' : 'text-gray-800 font-medium'}`}>
                                        {isEmpty ? 'Not set' : value}
                                    </span>
                                </div>
                            )}
                        </div>
                        {!isEditing && (
                            <button 
                                onClick={() => startEditing(field, value)} 
                                className="flex-shrink-0 w-7 h-7 bg-gray-100 hover:bg-brand-green hover:text-white text-gray-400 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all"
                                title={`Edit ${label}`}
                            >
                                <i className="fas fa-pencil-alt text-xs"></i>
                            </button>
                        )}
                    </div>
                );
            };

            return (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" onClick={onClose}>
                    {toastMessage && (
                        <div className="fixed top-6 right-6 z-[60] bg-brand-deep text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs font-extrabold border border-emerald-400/40 animate-bounce">
                            <i className="fas fa-check-circle text-brand-green text-base"></i>
                            <span>{toastMessage}</span>
                        </div>
                    )}
                    <div className="bg-gray-50/90 rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[92vh] border border-white/20 relative" onClick={e => e.stopPropagation()}>
                        
                        {/* Header Section */}
                        <div className="bg-white p-6 pb-4 border-b border-gray-200/80">
                            <div className="flex justify-between items-start mb-4">
                                <div className="flex items-center gap-4">
                                    {/* Avatar */}
                                    <div className="relative">
                                        <div className="w-14 h-14 rounded-2xl bg-brand-green flex items-center justify-center text-white text-xl font-black shadow-md">
                                            {(influencer.full_name || influencer.username || 'U').charAt(0).toUpperCase()}
                                        </div>
                                        <div className="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-sm">
                                            <PlatformIcon platform={influencer.platform} username={influencer.username} fullName={influencer.full_name} />
                                        </div>
                                    </div>

                                    {/* Info & Dropdowns */}
                                    <div>
                                        <div className="flex flex-wrap items-center gap-3 mb-1">
                                            <h2 className="text-xl font-black text-gray-900">
                                                {influencer.full_name || influencer.username || 'Unknown Influencer'}
                                            </h2>
                                            
                                            {/* Pipeline Status Dropdown */}
                                            <div className="flex items-center gap-1.5">
                                                <span className="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Status:</span>
                                                <div className="relative">
                                                    <select 
                                                        className="appearance-none bg-emerald-50 border border-emerald-300 text-brand-deep text-xs font-bold rounded-xl px-3 py-1 pr-7 focus:outline-none focus:ring-2 focus:ring-brand-green/30 cursor-pointer"
                                                        value={influencer.pipeline_status || 'For Order Creation'}
                                                        onChange={handleStatusChange}
                                                        disabled={saving}
                                                    >
                                                        {Object.keys(STAGE_CONFIG).map(stage => (
                                                            <option key={stage} value={stage}>{stage}</option>
                                                        ))}
                                                    </select>
                                                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-brand-green">
                                                        <i className="fas fa-chevron-down text-[9px]"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Campaign Type Dropdown */}
                                            <div className="flex items-center gap-1.5">
                                                <span className="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Type:</span>
                                                <div className="relative">
                                                    <select 
                                                        className="appearance-none bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl px-3 py-1 pr-7 focus:outline-none focus:ring-2 focus:ring-brand-green/30 cursor-pointer"
                                                        value={influencer.campaign_type || ''}
                                                        onChange={handleCampaignTypeChange}
                                                        disabled={saving}
                                                    >
                                                        <option value="">Select...</option>
                                                        <option value="Gifting">Gifting</option>
                                                        <option value="Paid">Paid</option>
                                                        <option value="Affiliate">Affiliate</option>
                                                    </select>
                                                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                                        <i className="fas fa-chevron-down text-[9px]"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Sub-header email & handle */}
                                        <div className="flex items-center gap-3 text-xs text-gray-500 font-medium">
                                            <span className="text-brand-green font-bold">@{influencer.username}</span>
                                            <span>•</span>
                                            <span>{influencer.email || 'No email provided'}</span>
                                        </div>
                                    </div>
                                </div>

                                {/* Close Button */}
                                <button onClick={onClose} className="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                                    <i className="fas fa-times text-sm"></i>
                                </button>
                            </div>

                            {/* Tabs Navigation */}
                            <div className="flex gap-6 border-b border-gray-200 mt-4">
                                <button 
                                    onClick={() => setActiveTab('basic')}
                                    className={`pb-3 text-xs font-extrabold border-b-2 transition-colors ${activeTab === 'basic' ? 'border-brand-green text-brand-deep' : 'border-transparent text-gray-400 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-id-card mr-1.5"></i>Profile & Metrics
                                </button>
                                <button 
                                    onClick={() => setActiveTab('orders')}
                                    className={`pb-3 text-xs font-extrabold border-b-2 transition-colors ${activeTab === 'orders' ? 'border-brand-green text-brand-deep' : 'border-transparent text-gray-400 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-shopping-bag mr-1.5"></i>Order Details
                                </button>
                                <button 
                                    onClick={() => setActiveTab('insight')}
                                    className={`pb-3 text-xs font-extrabold border-b-2 transition-colors ${activeTab === 'insight' ? 'border-brand-green text-brand-deep' : 'border-transparent text-gray-400 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-chart-bar mr-1.5"></i>Post Insight
                                </button>
                                <button 
                                    onClick={() => setActiveTab('stats')}
                                    className={`pb-3 text-xs font-extrabold border-b-2 transition-colors ${activeTab === 'stats' ? 'border-brand-green text-brand-deep' : 'border-transparent text-gray-400 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-chart-line mr-1.5"></i>Statistics
                                </button>
                                <button 
                                    onClick={() => setActiveTab('tasks')}
                                    className={`pb-3 text-xs font-extrabold border-b-2 transition-colors ${activeTab === 'tasks' ? 'border-brand-green text-brand-deep' : 'border-transparent text-gray-400 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-tasks mr-1.5"></i>Tasks
                                </button>
                            </div>
                        </div>

                        {/* Content Area */}
                        <div className="p-6 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/70">
                            {activeTab === 'basic' && (
                                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in">
                                    
                                    {/* LEFT COLUMN (Inspired by User Mockup) */}
                                    <div className="lg:col-span-5 space-y-4">
                                        
                                        {/* 1. Creator Info Header Card */}
                                        <div className="bg-white rounded-3xl border border-gray-200/80 p-5 shadow-sm">
                                            <div className="flex items-start gap-4">
                                                <div className="relative flex-shrink-0">
                                                    <img 
                                                        src={influencer.profile_image || `https://ui-avatars.com/api/?name=${encodeURIComponent(influencer.username || 'Creator')}&background=1FAE5B&color=fff`} 
                                                        alt={influencer.username}
                                                        onError={(e) => { e.target.onerror = null; e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(influencer.username || 'Creator')}&background=1FAE5B&color=fff`; }}
                                                        className="w-16 h-16 rounded-full object-cover border-2 border-emerald-100 shadow-sm"
                                                    />
                                                    <div className="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-white flex items-center justify-center shadow-sm border border-gray-100">
                                                        <PlatformIcon platform={influencer.platform} username={influencer.username} fullName={influencer.full_name} />
                                                    </div>
                                                </div>

                                                <div className="flex-1 min-w-0">
                                                    {editingField === 'full_name' ? (
                                                        <div className="flex items-center gap-1.5 mb-1.5">
                                                            <input 
                                                                type="text"
                                                                value={editValue}
                                                                onChange={(e) => setEditValue(e.target.value)}
                                                                onKeyDown={(e) => {
                                                                    if (e.key === 'Enter') saveEditing();
                                                                    if (e.key === 'Escape') setEditingField(null);
                                                                }}
                                                                placeholder="Enter creator name..."
                                                                className="text-xs font-bold px-2 py-1 border border-brand-green rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-green bg-emerald-50/20 text-gray-900 w-full"
                                                                autoFocus
                                                            />
                                                            <button 
                                                                onClick={saveEditing}
                                                                disabled={saving}
                                                                className="w-6 h-6 rounded-lg bg-brand-green hover:bg-brand-deep text-white flex items-center justify-center text-[10px] transition-colors flex-shrink-0"
                                                                title="Save Name"
                                                            >
                                                                <i className={saving ? "fas fa-spinner fa-spin" : "fas fa-check"}></i>
                                                            </button>
                                                            <button 
                                                                onClick={() => setEditingField(null)}
                                                                disabled={saving}
                                                                className="w-6 h-6 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-[10px] transition-colors flex-shrink-0"
                                                                title="Cancel"
                                                            >
                                                                <i className="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    ) : (
                                                        <div className="flex items-center gap-2">
                                                            <h3 className="text-base font-black text-gray-900 truncate">
                                                                {influencer.full_name || influencer.username || 'Unknown'}
                                                            </h3>
                                                            <button 
                                                                onClick={() => startEditing('full_name', influencer.full_name || '')}
                                                                className="w-6 h-6 rounded-full bg-emerald-50 text-brand-deep hover:bg-emerald-100 flex items-center justify-center text-[10px] transition-all flex-shrink-0"
                                                                title="Edit Name"
                                                            >
                                                                <i className="fas fa-pen"></i>
                                                            </button>
                                                        </div>
                                                    )}

                                                    <div className="flex items-center gap-1.5 text-xs text-brand-green font-bold mt-0.5">
                                                        <span>@{influencer.username || 'username'}</span>
                                                        <PlatformIcon platform={influencer.platform} username={influencer.username} fullName={influencer.full_name} />
                                                    </div>

                                                    <p className="text-xs text-gray-500 mt-2 font-medium leading-relaxed">
                                                        <i className="fas fa-map-marker-alt text-rose-500 mr-1"></i>
                                                        {influencer.location ? `${influencer.location} • ` : ''}
                                                        {influencer.notes ? influencer.notes : 'No bio available.'}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Quick Stats Row */}
                                            <div className="grid grid-cols-3 gap-2 mt-5 pt-4 border-t border-gray-100 text-center">
                                                <div>
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Posts</p>
                                                    <p className="text-xs font-black text-gray-800">{influencer.posts_count ? formatNumber(influencer.posts_count) : 'N/A'}</p>
                                                </div>
                                                <div>
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Followers</p>
                                                    <p className="text-xs font-black text-brand-green">{formatNumber(influencer.followers)}</p>
                                                </div>
                                                <div>
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Following</p>
                                                    <p className="text-xs font-black text-gray-800">{influencer.following ? formatNumber(influencer.following) : 'N/A'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        {/* 2. Email Box Card */}
                                        {editingField === 'email' ? (
                                            <div className="bg-white rounded-2xl border-2 border-brand-green p-2.5 shadow-sm flex items-center gap-2 animate-fade-in">
                                                <div className="flex items-center pl-1 text-brand-green flex-shrink-0">
                                                    <i className="fas fa-envelope text-xs"></i>
                                                </div>
                                                <input 
                                                    type="email"
                                                    value={editValue}
                                                    onChange={(e) => setEditValue(e.target.value)}
                                                    onKeyDown={(e) => {
                                                        if (e.key === 'Enter') saveEditing();
                                                        if (e.key === 'Escape') setEditingField(null);
                                                    }}
                                                    placeholder="Enter email address..."
                                                    className="flex-1 bg-transparent text-xs font-semibold text-gray-800 placeholder-gray-400 focus:outline-none min-w-0 px-1 py-0.5"
                                                    autoFocus
                                                />
                                                <div className="flex items-center gap-1.5 flex-shrink-0">
                                                    <button 
                                                        onClick={saveEditing} 
                                                        disabled={saving}
                                                        className="w-7 h-7 rounded-xl bg-brand-green hover:bg-brand-deep text-white flex items-center justify-center text-xs transition-all shadow-xs disabled:opacity-50"
                                                        title="Save Email (Enter)"
                                                    >
                                                        <i className={saving ? "fas fa-spinner fa-spin text-xs" : "fas fa-check text-xs"}></i>
                                                    </button>
                                                    <button 
                                                        onClick={() => setEditingField(null)} 
                                                        disabled={saving}
                                                        className="w-7 h-7 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-xs transition-colors"
                                                        title="Cancel (Esc)"
                                                    >
                                                        <i className="fas fa-times text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        ) : (
                                            <div className="bg-white rounded-2xl border border-gray-200/80 p-3.5 shadow-sm flex items-center justify-between gap-3">
                                                <div className="flex items-center gap-2 text-xs font-bold text-gray-700 truncate min-w-0">
                                                    <i className="fas fa-envelope text-brand-green flex-shrink-0"></i>
                                                    {influencer.email ? (
                                                        <a 
                                                            href={`mailto:${influencer.email}`} 
                                                            className="truncate text-gray-800 hover:text-brand-green font-bold transition-colors" 
                                                            title={`Send email to ${influencer.email}`}
                                                        >
                                                            {influencer.email}
                                                        </a>
                                                    ) : (
                                                        <span 
                                                            onClick={() => startEditing('email', '')}
                                                            className="text-gray-400 font-semibold cursor-pointer hover:text-gray-600 transition-colors"
                                                            title="Click to add email"
                                                        >
                                                            No email provided
                                                        </span>
                                                    )}
                                                </div>
                                                <div className="flex items-center gap-1.5 flex-shrink-0">
                                                    {influencer.email && (
                                                        <a 
                                                            href={`mailto:${influencer.email}`} 
                                                            className="w-7 h-7 rounded-xl bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep flex items-center justify-center text-xs transition-colors"
                                                            title="Send Email"
                                                        >
                                                            <i className="fas fa-paper-plane text-[10px]"></i>
                                                        </a>
                                                    )}
                                                    {influencer.email && (
                                                        <button 
                                                            onClick={() => copyToClipboard(influencer.email)} 
                                                            className="w-7 h-7 rounded-xl bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep flex items-center justify-center text-xs transition-colors"
                                                            title="Copy Email"
                                                        >
                                                            <i className="far fa-copy"></i>
                                                        </button>
                                                    )}
                                                    <button 
                                                        onClick={() => startEditing('email', influencer.email || '')} 
                                                        className="w-7 h-7 rounded-xl bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep flex items-center justify-center text-xs transition-colors"
                                                        title={influencer.email ? "Edit Email" : "Add Email"}
                                                    >
                                                        <i className="fas fa-pen"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        )}

                                        {/* 3. Interactive Action Cards */}
                                        <div className="space-y-3">
                                            {/* Generate Discount Code */}
                                            <div className="bg-white rounded-2xl border border-gray-200/80 p-3.5 shadow-sm flex items-center justify-between">
                                                <div className="flex items-center gap-2.5 min-w-0">
                                                    <span className="text-base">🏷️</span>
                                                    <div className="min-w-0">
                                                        <p className="text-xs font-extrabold text-gray-900">Generate Discount Code</p>
                                                        <p className="text-[11px] font-bold text-brand-green truncate">{influencer.discount_code || 'None generated yet'}</p>
                                                    </div>
                                                </div>
                                                <button 
                                                    onClick={handleGenerateDiscount} 
                                                    className="w-8 h-8 rounded-full bg-emerald-100 text-brand-deep hover:bg-brand-deep hover:text-white flex items-center justify-center text-xs font-bold transition-all shadow-xs flex-shrink-0"
                                                    title="Add / Generate Discount Code"
                                                >
                                                    <i className="fas fa-plus"></i>
                                                </button>
                                            </div>

                                            {/* Generate Affiliate Link */}
                                            <div className="bg-white rounded-2xl border border-gray-200/80 p-3.5 shadow-sm flex items-center justify-between">
                                                <div className="flex items-center gap-2.5 min-w-0">
                                                    <span className="text-base">🔗</span>
                                                    <div className="min-w-0">
                                                        <p className="text-xs font-extrabold text-gray-900">Generate Affiliate Link</p>
                                                        <p className="text-[11px] font-bold text-brand-green truncate max-w-[160px]">{influencer.affiliate_link || 'None generated yet'}</p>
                                                    </div>
                                                </div>
                                                <button 
                                                    onClick={handleGenerateAffiliate} 
                                                    className="w-8 h-8 rounded-full bg-emerald-100 text-brand-deep hover:bg-brand-deep hover:text-white flex items-center justify-center text-xs font-bold transition-all shadow-xs flex-shrink-0"
                                                    title="Add / Generate Affiliate Link"
                                                >
                                                    <i className="fas fa-plus"></i>
                                                </button>
                                            </div>

                                            {/* Tags Card */}
                                            <div className="bg-white rounded-2xl border border-gray-200/80 p-3.5 shadow-sm">
                                                <div className="flex items-center justify-between mb-2.5">
                                                    <div className="flex items-center gap-2">
                                                        <span className="text-base">🎩</span>
                                                        <p className="text-xs font-extrabold text-gray-900">Tags</p>
                                                    </div>
                                                    <button 
                                                        onClick={handleAddTag}
                                                        className="w-8 h-8 rounded-full bg-emerald-100 text-brand-deep hover:bg-brand-deep hover:text-white flex items-center justify-center text-xs font-bold transition-all shadow-xs flex-shrink-0"
                                                        title="Add Tag"
                                                    >
                                                        <i className="fas fa-plus"></i>
                                                    </button>
                                                </div>

                                                <div className="flex flex-wrap gap-2">
                                                    <span className="inline-flex items-center gap-1 bg-emerald-100 text-brand-deep border border-emerald-200 font-bold text-[11px] px-2.5 py-1 rounded-xl">
                                                        <span>{influencer.pipeline_status || 'Active'}</span>
                                                    </span>
                                                    {influencer.campaign_type && (
                                                        <span className="inline-flex items-center gap-1 bg-purple-100 text-purple-800 border border-purple-200 font-bold text-[11px] px-2.5 py-1 rounded-xl">
                                                            <span>{influencer.campaign_type}</span>
                                                        </span>
                                                    )}
                                                    {(influencer.niche ? influencer.niche.split(',').map(t => t.trim()).filter(Boolean) : []).map((tag, idx) => (
                                                        <span key={idx} className="inline-flex items-center gap-1.5 bg-amber-50 text-amber-900 border border-amber-200 font-bold text-[11px] px-2.5 py-1 rounded-xl group">
                                                            <span>{tag}</span>
                                                            <button 
                                                                onClick={() => handleRemoveTag(tag)}
                                                                className="text-amber-500 hover:text-rose-600 transition-colors text-[10px]"
                                                                title={`Remove tag '${tag}'`}
                                                            >
                                                                <i className="fas fa-times"></i>
                                                            </button>
                                                        </span>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>

                                        {/* View Full Profile CTA */}
                                        <a 
                                            href={influencer.platform === 'YouTube' ? `https://youtube.com/@${influencer.username}` : (influencer.platform === 'TikTok' ? `https://tiktok.com/@${influencer.username}` : `https://instagram.com/${influencer.username}`)}
                                            target="_blank"
                                            className="w-full py-3 px-4 rounded-2xl border-2 border-brand-green text-brand-deep hover:bg-brand-deep hover:text-white font-extrabold text-xs transition-all flex items-center justify-center gap-2 shadow-xs group bg-white"
                                        >
                                            <span>View Full Profile</span>
                                            <i className="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>

                                    {/* RIGHT COLUMN (Metrics Grid & Notes) */}
                                    <div className="lg:col-span-7 space-y-5">
                                        
                                        {/* Executive Analytics Metrics Cards Grid */}
                                        <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                            {/* Eng. Rate */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Eng. Rate</span>
                                                    <span className="text-[9px] font-bold bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded-full">Excellent</span>
                                                </div>
                                                <p className="text-lg font-black text-gray-900">{influencer.eng_rate ? parseFloat(influencer.eng_rate).toFixed(2) : '5.39'}%</p>
                                            </div>

                                            {/* Fair Price */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Fair Price</span>
                                                    <i className="fas fa-info-circle text-gray-300 text-xs" title="Estimated fair sponsorship rate"></i>
                                                </div>
                                                {(() => {
                                                    const rawRate = parseFloat(influencer.est_post_rate);
                                                    const price = (!isNaN(rawRate) && rawRate > 0) 
                                                        ? rawRate 
                                                        : Math.max(50, Math.round((parseFloat(influencer.followers) || 0) * 0.15));
                                                    return <p className="text-lg font-black text-brand-green">${formatNumber(price)}</p>;
                                                })()}
                                            </div>

                                            {/* Growth Rate */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Growth Rate</span>
                                                </div>
                                                <p className="text-lg font-black text-gray-900">+2.4%</p>
                                            </div>

                                            {/* Diehard Fans */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Diehard Fans</span>
                                                    <i className="fas fa-info-circle text-gray-300 text-xs" title="Highly engaged core followers"></i>
                                                </div>
                                                <p className="text-lg font-black text-gray-400">N/A</p>
                                            </div>

                                            {/* Total Likes */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Total Likes</span>
                                                </div>
                                                <p className="text-lg font-black text-gray-900">{formatNumber(Math.round((parseFloat(influencer.followers) || 0) * 0.4) || 4800)}</p>
                                            </div>

                                            {/* Predicted Cmts */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Predicted Cmts</span>
                                                </div>
                                                <p className="text-lg font-black text-gray-400">N/A</p>
                                            </div>

                                            {/* Avg Likes */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Avg. Likes</span>
                                                </div>
                                                <p className="text-lg font-black text-gray-900">{formatNumber(Math.round((parseFloat(influencer.avg_video_views) || 0) * 0.1) || 1200)}</p>
                                            </div>

                                            {/* Avg Comments */}
                                            <div className="bg-white rounded-2xl p-3.5 border border-gray-200/80 shadow-sm">
                                                <div className="flex items-center justify-between mb-1">
                                                    <span className="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Avg. Comments</span>
                                                </div>
                                                <p className="text-lg font-black text-gray-900">{formatNumber(Math.round((parseFloat(influencer.followers) || 0) * 0.01) || 84)}</p>
                                            </div>
                                        </div>

                                        {/* Collaboration Details Custom Fields Card */}
                                        <div className="bg-white rounded-3xl border border-gray-200/80 p-5 shadow-sm">
                                            <div className="flex items-center gap-2 mb-4">
                                                <span className="text-base">💼</span>
                                                <h4 className="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Collaboration & Custom Fields</h4>
                                            </div>
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label className="block text-[10px] text-gray-400 font-bold uppercase mb-1">Deliverables</label>
                                                    <input 
                                                        type="text" 
                                                        className="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none" 
                                                        placeholder="e.g. 1x Reel, 2x Stories" 
                                                        value={deliverables} 
                                                        onChange={(e) => setDeliverables(e.target.value)}
                                                        onBlur={handleDeliverablesBlur}
                                                    />
                                                </div>
                                                <div>
                                                    <label className="block text-[10px] text-gray-400 font-bold uppercase mb-1">Fee ($)</label>
                                                    <input 
                                                        type="number" 
                                                        className="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none" 
                                                        placeholder="e.g. 1500" 
                                                        value={fee} 
                                                        onChange={(e) => setFee(e.target.value)}
                                                        onBlur={handleFeeBlur}
                                                    />
                                                </div>
                                                <div className="sm:col-span-2">
                                                    <label className="block text-[10px] text-gray-400 font-bold uppercase mb-1">Post Deadline</label>
                                                    <input 
                                                        type="date" 
                                                        className="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none" 
                                                        value={deadline} 
                                                        onChange={(e) => setDeadline(e.target.value)}
                                                        onBlur={handleDeadlineBlur}
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {/* Interactive Notes Section */}
                                        <div className="bg-white rounded-3xl border border-gray-200/80 p-5 shadow-sm">
                                            <div className="flex items-center justify-between mb-3">
                                                <div className="flex items-center gap-2">
                                                    <span className="text-base">📝</span>
                                                    <h4 className="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Notes</h4>
                                                    {saving && (
                                                        <span className="text-[10px] text-gray-400 flex items-center gap-1">
                                                            <div className="w-2.5 h-2.5 border-2 border-gray-300 border-t-brand-green rounded-full animate-spin"></div>
                                                            Saving...
                                                        </span>
                                                    )}
                                                </div>
                                                <button 
                                                    onClick={() => {
                                                        const noteEl = document.getElementById('creator-note-textarea');
                                                        if (noteEl) noteEl.focus();
                                                    }}
                                                    className="text-xs font-extrabold text-brand-deep hover:text-brand-green transition-colors flex items-center gap-1"
                                                >
                                                    <i className="fas fa-plus text-[10px]"></i> Take a Note
                                                </button>
                                            </div>

                                            <textarea 
                                                id="creator-note-textarea"
                                                className="w-full border border-gray-200 rounded-2xl p-4 text-xs font-medium focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all resize-none bg-gray-50/50 hover:bg-white leading-relaxed text-gray-800"
                                                rows="6"
                                                placeholder="Enter text here... Add campaign notes, communication logs, or influencer requirements."
                                                value={noteContent}
                                                onChange={(e) => setNoteContent(e.target.value)}
                                                onBlur={handleNoteBlur}
                                            ></textarea>

                                            <div className="mt-3.5 flex items-center justify-between">
                                                <div className="flex items-center gap-2">
                                                    <button 
                                                        onClick={handleNoteBlur}
                                                        className="rounded-xl bg-brand-deep hover:bg-brand-green text-white font-bold text-xs px-5 py-2 transition-all shadow-xs"
                                                    >
                                                        Save
                                                    </button>
                                                    <button 
                                                        onClick={() => setNoteContent(influencer.notes || '')}
                                                        className="rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-2 transition-all"
                                                    >
                                                        Cancel
                                                    </button>
                                                </div>

                                                <input 
                                                    type="file" 
                                                    ref={fileInputRef} 
                                                    className="hidden" 
                                                    onChange={handleFileSelected} 
                                                />
                                                <button 
                                                    onClick={() => fileInputRef.current && fileInputRef.current.click()}
                                                    className="text-gray-400 hover:text-brand-deep transition-colors text-sm p-1.5 rounded-lg hover:bg-gray-100"
                                                    title="Attach File"
                                                >
                                                    <i className="fas fa-paperclip"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            )}

                            {activeTab === 'orders' && (
                                <div className="space-y-6 animate-fade-in">
                                    <h3 className="text-lg font-bold text-gray-900 mb-4">Order Details</h3>
                                    <div className="grid grid-cols-1 gap-4">
                                        {renderEditableRow('Order #', 'order_number', influencer.order_number)}
                                        {renderEditableRow('Product', 'product_name', influencer.product_name)}
                                        {renderEditableRow('Cost ($)', 'product_cost', influencer.product_cost, null, 'number')}
                                        {renderEditableRow('Discount Code', 'discount_code', influencer.discount_code)}
                                        {renderEditableRow('Affiliate Link', 'affiliate_link', influencer.affiliate_link)}
                                        {renderEditableRow('Tracking Link', 'tracking_link', influencer.tracking_link)}
                                        {renderEditableRow('Shipping Address', 'shipping_address', influencer.shipping_address, null, 'textarea')}
                                    </div>
                                </div>
                            )}

                            {activeTab === 'insight' && (
                                <div className="space-y-6 animate-fade-in">
                                    <h3 className="text-lg font-bold text-gray-900 mb-4">Post Insights</h3>
                                    <div className="grid grid-cols-1 gap-4">
                                        {renderEditableRow('Avg. Views', 'avg_video_views', influencer.avg_video_views, null, 'number')}
                                        {renderEditableRow('GMV ($)', 'gmv', influencer.gmv, null, 'number')}
                                        {renderEditableRow('Est. Post Rate', 'est_post_rate', influencer.est_post_rate, null, 'number')}
                                    </div>
                                </div>
                            )}

                            {activeTab === 'stats' && (
                                <div className="space-y-6 animate-fade-in">
                                    <h3 className="text-lg font-bold text-gray-900 mb-4">Statistics</h3>
                                    <div className="grid grid-cols-1 gap-4">
                                        {renderEditableRow('Followers', 'followers', influencer.followers, null, 'number')}
                                        {renderEditableRow('Eng. Rate (%)', 'engagement_rate', influencer.eng_rate, null, 'number')}
                                    </div>
                                </div>
                            )}

                            {activeTab === 'tasks' && (
                                <div className="space-y-6 animate-fade-in">
                                    <div className="flex justify-between items-center mb-4">
                                        <h3 className="text-lg font-bold text-gray-900">Campaign Tasks</h3>
                                        <button onClick={fetchTasks} className="text-brand-blue hover:text-blue-700 text-xs font-bold">
                                            <i className={`fas fa-sync-alt ${loadingTasks ? 'fa-spin' : ''} mr-1`}></i> Refresh
                                        </button>
                                    </div>
                                    
                                    {loadingTasks ? (
                                        <div className="py-10 text-center">
                                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-green mx-auto mb-2"></div>
                                            <p className="text-gray-400 text-sm">Loading tasks...</p>
                                        </div>
                                    ) : tasks.length > 0 ? (
                                        <div className="space-y-4">
                                            {/* Group by campaign if multiple */}
                                            {Array.from(new Set(tasks.map(t => t.campaign_title))).map(campTitle => (
                                                <div key={campTitle} className="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                                                    <div className="px-4 py-2 bg-white border-b border-gray-100 flex items-center justify-between">
                                                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Campaign</span>
                                                        <span className="text-sm font-bold text-brand-dark">{campTitle}</span>
                                                    </div>
                                                    <div className="p-3 space-y-2">
                                                        {tasks.filter(t => t.campaign_title === campTitle).map(task => (
                                                            <div key={task.id} className="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-50 hover:border-brand-green/30 transition-all group">
                                                                <div className="flex items-center gap-3">
                                                                    <button 
                                                                        onClick={() => toggleTaskStatus(task.id, task.status)}
                                                                        className={`w-5 h-5 rounded border-2 transition-all flex items-center justify-center ${task.status === 'Done' ? 'bg-brand-green border-brand-green text-white' : 'border-gray-300 hover:border-brand-green text-transparent'}`}
                                                                    >
                                                                        <i className="fas fa-check text-[10px]"></i>
                                                                    </button>
                                                                    <span className={`text-sm font-medium ${task.status === 'Done' ? 'text-gray-400 line-through' : 'text-gray-700'}`}>
                                                                        {task.task_name}
                                                                    </span>
                                                                </div>
                                                                <span className={`text-[10px] font-bold uppercase tracking-wider ${task.status === 'Done' ? 'text-brand-green' : 'text-orange-400'}`}>
                                                                    {task.status}
                                                                </span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                            <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                                <i className="fas fa-clipboard-list text-gray-300"></i>
                                            </div>
                                            <p className="text-gray-500 font-medium">No tasks assigned yet.</p>
                                            <p className="text-xs text-gray-400 mt-1">Check back later or contact your manager.</p>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>

                        {showEmailComposer && (
                            <div className="fixed bottom-6 right-6 z-50">
                                <div className="w-[700px] h-[700px] bg-white flex flex-col animate-fade-in rounded-2xl overflow-hidden shadow-2xl border border-gray-200 ring-1 ring-black/5">
                                    <div className="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                    <h3 className="font-bold text-lg text-gray-800">Email Conversation</h3>
                                    <div className="flex gap-2">
                                        <button onClick={handleSyncEmail} className="text-gray-500 hover:text-brand-blue p-2 rounded-full hover:bg-white transition-colors" title="Check for new emails">
                                            <i className={`fas fa-sync ${syncingEmail ? 'fa-spin' : ''}`}></i>
                                        </button>
                                        <button onClick={() => setShowEmailComposer(false)} className="text-gray-500 hover:text-red-500 p-2 rounded-full hover:bg-white transition-colors">
                                            <i className="fas fa-times"></i>
                                        </button>
                                    </div>
                                    </div>
                                    <div className="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-4">
                                        {emailHistory.length === 0 ? (
                            <div className="text-center text-gray-400 mt-10">No emails yet. Start the conversation!</div>
                        ) : (
                            emailHistory.map((email) => (
                                <div key={email.id} className={`flex flex-col ${email.direction === 'outbound' ? 'items-end' : 'items-start'}`}>
                                    <div className={`max-w-[85%] rounded-2xl p-4 shadow-sm ${email.direction === 'outbound' ? 'bg-brand-blue text-white rounded-tr-sm' : 'bg-white text-gray-800 rounded-tl-sm border border-gray-200'}`}>
                                        <div className="font-bold text-xs mb-1 opacity-80 border-b border-white/20 pb-1">{email.subject}</div>
                                        {/* Render HTML content safely */}
                                        <div className="text-sm whitespace-pre-wrap leading-relaxed email-content" dangerouslySetInnerHTML={{ __html: email.body }}></div>
                                        <div className="text-[10px] mt-2 opacity-60 text-right">{email.created_at}</div>
                                    </div>
                                </div>
                            ))
                        )}
                        <div ref={messagesEndRef} />
                    </div>

                    <div className="p-4 bg-white border-t border-gray-200 relative">
                        {showTemplates && (
                            <div className="absolute bottom-full left-4 mb-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-10 animate-fade-in">
                                <div className="p-2 bg-gray-50 border-b border-gray-100 font-bold text-xs text-gray-500 uppercase tracking-wide">Select Template</div>
                                <div className="max-h-60 overflow-y-auto">
                                    {EMAIL_TEMPLATES.map(template => (
                                        <button 
                                            key={template.id}
                                            onClick={() => applyTemplate(template)}
                                            className="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-brand-blue/5 hover:text-brand-blue transition-colors border-b border-gray-50 last:border-0"
                                        >
                                            {template.name}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                        <div className="flex gap-2 mb-3">
                             <button 
                                onClick={() => setShowTemplates(!showTemplates)}
                                className={`text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors flex items-center gap-2 ${showTemplates ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-blue hover:text-brand-blue'}`}
                            >
                                <i className="fas fa-magic"></i> Templates
                            </button>
                        </div>

                        <input 
                            type="text" 
                            placeholder="Subject" 
                            className="w-full mb-3 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue text-sm transition-all"
                                            value={emailSubject}
                                            onChange={(e) => setEmailSubject(e.target.value)}
                                        />
                                        <textarea 
                                            placeholder="Type your message..." 
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue text-sm h-32 resize-none transition-all"
                                            value={emailBody}
                                            onChange={(e) => setEmailBody(e.target.value)}
                                        ></textarea>
                                        <div className="flex justify-end mt-3">
                                            <button 
                                                onClick={handleSendEmail} 
                                                disabled={sendingEmail}
                                                className="bg-brand-blue text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-600 transition-all shadow-lg shadow-brand-blue/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                            >
                                                {sendingEmail ? 'Sending...' : <><i className="fas fa-paper-plane"></i> Send Email</>}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            );
        };

        const InfluencerCard = ({ influencer, unreadCount, onDragStart, onMoveToClosed, onMoveToStage, onViewProfile, addToast }) => {
            const [isDragging, setIsDragging] = useState(false);
            const [menuOpen, setMenuOpen] = useState(false);
            const menuRef = React.useRef(null);

            const handleDragStart = (e) => {
                setIsDragging(true);
                onDragStart(e, influencer);
            };

            const handleDragEnd = () => {
                setIsDragging(false);
            };

            useEffect(() => {
                if (!menuOpen) return;
                const onDoc = (e) => {
                    if (!menuRef.current) return;
                    if (!menuRef.current.contains(e.target)) setMenuOpen(false);
                };
                document.addEventListener('mousedown', onDoc);
                return () => document.removeEventListener('mousedown', onDoc);
            }, [menuOpen]);

            const copyText = async (text, label) => {
                const value = (text || '').toString();
                if (!value) return;
                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(value);
                        if (addToast) addToast(`${label} copied`, 'success');
                        return;
                    }
                } catch (e) {}

                try {
                    const ta = document.createElement('textarea');
                    ta.value = value;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    ta.style.top = '0';
                    document.body.appendChild(ta);
                    ta.focus();
                    ta.select();
                    const ok = document.execCommand('copy');
                    document.body.removeChild(ta);
                    if (ok && addToast) addToast(`${label} copied`, 'success');
                } catch (e) {
                    if (addToast) addToast('Copy failed', 'error');
                }
            };

            const getProfileUrl = () => {
                const p = (influencer.platform || '').toLowerCase();
                const u = (influencer.username || '').toString().replace(/^@+/, '');
                if (!u) return '#';
                if (p.includes('instagram')) return `https://instagram.com/${u}`;
                if (p.includes('tiktok')) return `https://tiktok.com/@${u}`;
                if (p.includes('youtube')) return `https://youtube.com/@${u}`;
                return '#';
            };

            return (
                <div 
                    className={`bg-white p-4 rounded-xl shadow-card hover:shadow-card-hover border border-gray-100 transition-all duration-200 cursor-grab active:cursor-grabbing group relative ${isDragging ? 'opacity-50 rotate-3 scale-95' : 'hover:-translate-y-1'}`}
                    draggable="true"
                    onDragStart={handleDragStart}
                    onDragEnd={handleDragEnd}
                >
                    <div className="flex items-start justify-between mb-3">
                        <div className="flex items-center gap-3">
                            <div className="relative">
                                <img 
                                    src={`https://ui-avatars.com/api/?name=${influencer.username}&background=random&color=fff&size=48&bold=true`} 
                                    alt={influencer.username} 
                                    className="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-sm"
                                />
                                {unreadCount > 0 && (
                                    <div className="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm z-10 animate-pulse">
                                        {unreadCount}
                                    </div>
                                )}
                                <div className="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5 shadow-sm">
                                    <div className="w-4 h-4 flex items-center justify-center rounded-full bg-gray-50 text-[10px]">
                                        <PlatformIcon platform={influencer.platform} />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 className="font-bold text-sm text-gray-800 leading-tight group-hover:text-brand-deep transition-colors cursor-pointer" onClick={() => {
                                    onViewProfile(influencer);
                                    // Optimistically clear notification if unreadCount > 0
                                    if (unreadCount > 0) {
                                         // We can trigger a refresh of counts or handle it inside onViewProfile
                                    }
                                }}>
                                    {influencer.username}
                                </h3>
                                <p className="text-[10px] text-gray-500 font-medium uppercase tracking-wide mt-0.5">
                                    {influencer.platform || 'Unknown'}
                                </p>
                            </div>
                        </div>
                        <div className="relative" ref={menuRef}>
                            <button
                                type="button"
                                onMouseDown={(e) => { e.preventDefault(); e.stopPropagation(); }}
                                onClick={(e) => { e.preventDefault(); e.stopPropagation(); setMenuOpen(v => !v); }}
                                className="text-gray-300 hover:text-gray-500 transition-colors p-1 rounded hover:bg-gray-50"
                                aria-label="More actions"
                            >
                                <i className="fas fa-ellipsis-v text-xs"></i>
                            </button>
                            {menuOpen && (
                                <div className="absolute right-0 top-8 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                                    <button
                                        type="button"
                                        onMouseDown={(e) => { e.preventDefault(); e.stopPropagation(); }}
                                        onClick={(e) => { e.preventDefault(); e.stopPropagation(); setMenuOpen(false); onViewProfile(influencer); }}
                                        className="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                    >
                                        <i className="far fa-eye text-gray-400"></i>
                                        View Details
                                    </button>
                                    <button
                                        type="button"
                                        onMouseDown={(e) => { e.preventDefault(); e.stopPropagation(); }}
                                        onClick={(e) => { e.preventDefault(); e.stopPropagation(); setMenuOpen(false); copyText(influencer.username, 'Username'); }}
                                        className="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                    >
                                        <i className="far fa-copy text-gray-400"></i>
                                        Copy Username
                                    </button>
                                    <button
                                        type="button"
                                        onMouseDown={(e) => { e.preventDefault(); e.stopPropagation(); }}
                                        onClick={(e) => { e.preventDefault(); e.stopPropagation(); setMenuOpen(false); copyText(getProfileUrl(), 'Profile link'); }}
                                        className="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                    >
                                        <i className="fas fa-link text-gray-400"></i>
                                        Copy Profile Link
                                    </button>
                                    <div className="border-t border-gray-100"></div>
                                    <div className="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Move To</div>
                                    {['For Outreach', 'Contacted', 'Replied', 'In - Progress', 'Not Interested'].map(stage => (
                                        <button
                                            key={stage}
                                            type="button"
                                            onMouseDown={(e) => { e.preventDefault(); e.stopPropagation(); }}
                                            onClick={(e) => { e.preventDefault(); e.stopPropagation(); setMenuOpen(false); if (onMoveToStage) onMoveToStage(influencer.id, stage); }}
                                            className="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-between"
                                        >
                                            <span>{stage}</span>
                                            {influencer.pipeline_status === stage ? <i className="fas fa-check text-xs text-brand-green"></i> : null}
                                        </button>
                                    ))}
                                    <div className="border-t border-gray-100"></div>
                                    <button
                                        type="button"
                                        onMouseDown={(e) => { e.preventDefault(); e.stopPropagation(); }}
                                        onClick={(e) => { e.preventDefault(); e.stopPropagation(); setMenuOpen(false); onMoveToClosed(influencer.id); }}
                                        className="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                    >
                                        <i className="fas fa-box text-gray-400"></i>
                                        Move to Closed
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                    
                    <div className="flex items-center gap-2 mb-3">
                        <div className="px-2 py-1 bg-gray-50 rounded-md border border-gray-100 flex items-center gap-1.5" title="Followers">
                            <i className="fas fa-users text-[10px] text-gray-400"></i>
                            <span className="text-xs font-semibold text-gray-600">{influencer.followers || '0'}</span>
                        </div>
                        <div className="px-2 py-1 bg-gray-50 rounded-md border border-gray-100 flex items-center gap-1.5" title="Engagement Rate">
                            <i className="fas fa-chart-line text-[10px] text-gray-400"></i>
                            <span className="text-xs font-semibold text-gray-600">{influencer.eng_rate || '0'}%</span>
                        </div>
                    </div>
                    
                    {influencer.notes && (
                        <div className="text-xs text-gray-500 bg-yellow-50/50 p-2 rounded-lg border border-yellow-100/50 line-clamp-2 italic">
                            "{influencer.notes}"
                        </div>
                    )}

                    {/* Footer Actions */}
                    <div className="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                        <span className="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Updated today</span>
                        
                        {influencer.pipeline_status === 'In - Progress' ? (
                            <button 
                                onClick={() => onMoveToClosed(influencer.id)}
                                className="text-[10px] font-semibold text-white bg-brand-green hover:bg-green-700 px-2 py-1 rounded transition-colors shadow-sm flex items-center gap-1"
                                title="Move to Closed Collaborations"
                            >
                                Start Collab <i className="fas fa-arrow-right"></i>
                            </button>
                        ) : (
                            <button 
                                onClick={() => onViewProfile(influencer)}
                                className="text-xs font-medium text-brand-green hover:underline opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                View Details
                            </button>
                        )}
                    </div>
                </div>
            );
        };

        const SpreadsheetListView = ({ displayData, selectedIds, setSelectedIds, onUpdateStage, onUpdateDetails, onViewProfile, onDelete }) => {
            const allInfluencers = Object.values(displayData).flat();
            const validStages = [
                'For Outreach', 'Contacted', 'Replied', 'In - Progress', 'Not Interested',
                'For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed'
            ];

            const isAllSelected = allInfluencers.length > 0 && selectedIds.length === allInfluencers.length;

            const handleSelectAll = (e) => {
                if (e.target.checked) {
                    setSelectedIds(allInfluencers.map(inf => inf.id));
                } else {
                    setSelectedIds([]);
                }
            };

            const handleSelectRow = (id, checked) => {
                if (checked) {
                    setSelectedIds(prev => [...prev, id]);
                } else {
                    setSelectedIds(prev => prev.filter(selectedId => selectedId !== id));
                }
            };

            const handleStageChange = (id, newStage) => {
                onUpdateStage(id, newStage);
            };

            const handleFieldChange = (id, field, value) => {
                onUpdateDetails(id, { [field]: value });
            };

            return (
                <div className="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden flex-1 flex flex-col mb-8" style={{ width: 'calc(100vw - 290px)' }}>
                    <div className="overflow-x-auto flex-1 custom-scrollbar">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/50 text-[10px] font-extrabold text-gray-400 uppercase tracking-wider sticky top-0 z-10">
                                    <th className="py-4 px-6 w-12 text-center">
                                        <input 
                                            type="checkbox" 
                                            className="rounded border-gray-300 text-brand-green focus:ring-brand-green/20"
                                            checked={isAllSelected}
                                            onChange={handleSelectAll}
                                        />
                                    </th>
                                    <th className="py-4 px-4 w-72">Creator Profile</th>
                                    <th className="py-4 px-4 w-52">Current Stage</th>
                                    <th className="py-4 px-4 w-60">Deliverables</th>
                                    <th className="py-4 px-4 w-32">Fee ($)</th>
                                    <th className="py-4 px-4 w-44">Post Deadline</th>
                                    <th className="py-4 px-4 w-44">Campaign Type</th>
                                    <th className="py-4 px-4 w-28 text-right pr-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100/80 text-xs">
                                {allInfluencers.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="py-16 text-center text-gray-400 font-medium">
                                            No influencers found in this outreach campaign.
                                        </td>
                                    </tr>
                                ) : (
                                    allInfluencers.map(inf => {
                                        const isChecked = selectedIds.includes(inf.id);
                                        return (
                                            <tr key={inf.id} className={`hover:bg-gray-50/40 transition-colors ${isChecked ? 'bg-emerald-50/10' : ''}`}>
                                                <td className="py-4 px-6 text-center">
                                                    <input 
                                                        type="checkbox" 
                                                        className="rounded border-gray-300 text-brand-green focus:ring-brand-green/20"
                                                        checked={isChecked}
                                                        onChange={(e) => handleSelectRow(inf.id, e.target.checked)}
                                                    />
                                                </td>
                                                <td className="py-4 px-4">
                                                    <div className="flex items-center gap-3">
                                                        <img 
                                                            src={inf.profile_image || `https://ui-avatars.com/api/?name=${encodeURIComponent(inf.username || 'Creator')}&background=1FAE5B&color=fff`} 
                                                            alt={inf.username}
                                                            className="w-8 h-8 rounded-full object-cover border border-gray-200"
                                                            onError={(e) => { e.target.onerror = null; e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(inf.username || 'Creator')}&background=1FAE5B&color=fff`; }}
                                                        />
                                                        <div className="min-w-0">
                                                            <div className="font-extrabold text-gray-800 truncate flex items-center gap-1.5">
                                                                <span>{inf.full_name || inf.username}</span>
                                                                <span className={`text-[9px] font-bold px-1.5 py-0.5 rounded-md ${
                                                                    inf.platform === 'Instagram' ? 'bg-pink-50 text-pink-700' :
                                                                    inf.platform === 'TikTok' ? 'bg-black text-white' :
                                                                    inf.platform === 'YouTube' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700'
                                                                }`}>
                                                                    {inf.platform}
                                                                </span>
                                                            </div>
                                                            <div className="text-[11px] text-gray-400 font-medium">@{inf.username}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="py-4 px-4">
                                                    <select 
                                                        value={inf.pipeline_status || 'For Outreach'} 
                                                        onChange={(e) => handleStageChange(inf.id, e.target.value)}
                                                        className="bg-transparent border border-gray-200 rounded-lg px-2 py-1 text-xs font-bold text-gray-700 focus:outline-none focus:border-brand-green"
                                                    >
                                                        {validStages.map(stg => (
                                                            <option key={stg} value={stg}>{stg}</option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td className="py-4 px-4">
                                                    <input 
                                                        type="text" 
                                                        defaultValue={inf.deliverables || ''} 
                                                        placeholder="Add deliverables..."
                                                        onBlur={(e) => handleFieldChange(inf.id, 'deliverables', e.target.value)}
                                                        className="w-full bg-transparent border-b border-transparent hover:border-gray-200 focus:border-brand-green px-1 py-0.5 outline-none transition-colors"
                                                    />
                                                </td>
                                                <td className="py-4 px-4">
                                                    <div className="flex items-center gap-1">
                                                        <span className="text-gray-400 font-bold">$</span>
                                                        <input 
                                                            type="number" 
                                                            defaultValue={inf.fee || ''} 
                                                            placeholder="0"
                                                            onBlur={(e) => handleFieldChange(inf.id, 'fee', e.target.value)}
                                                            className="w-20 bg-transparent border-b border-transparent hover:border-gray-200 focus:border-brand-green px-1 py-0.5 outline-none transition-colors"
                                                        />
                                                    </div>
                                                </td>
                                                <td className="py-4 px-4">
                                                    <input 
                                                        type="date" 
                                                        defaultValue={inf.deadline || ''} 
                                                        onChange={(e) => handleFieldChange(inf.id, 'deadline', e.target.value)}
                                                        className="bg-transparent border-b border-transparent hover:border-gray-200 focus:border-brand-green px-1 py-0.5 outline-none transition-colors"
                                                    />
                                                </td>
                                                <td className="py-4 px-4">
                                                    <input 
                                                        type="text" 
                                                        defaultValue={inf.campaign_type || ''} 
                                                        placeholder="Add niche/type..."
                                                        onBlur={(e) => handleFieldChange(inf.id, 'campaign_type', e.target.value)}
                                                        className="w-full bg-transparent border-b border-transparent hover:border-gray-200 focus:border-brand-green px-1 py-0.5 outline-none transition-colors"
                                                    />
                                                </td>
                                                <td className="py-4 px-4 text-right pr-6">
                                                    <div className="flex items-center justify-end gap-1">
                                                        <button 
                                                            onClick={() => onViewProfile(inf)}
                                                            className="w-7 h-7 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-brand-deep transition-all"
                                                            title="View Profile Details"
                                                        >
                                                            <i className="fas fa-eye text-xs"></i>
                                                        </button>
                                                        <button 
                                                            onClick={() => onDelete(inf.id)}
                                                            className="w-7 h-7 rounded-full hover:bg-red-50 flex items-center justify-center text-gray-400 hover:text-red-600 transition-all"
                                                            title="Delete Collaboration"
                                                        >
                                                            <i className="fas fa-trash text-xs"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            );
        };

        const PipelineColumn = ({ title, influencers, unreadCounts, onDrop, onDragOver, onMoveToClosed, onMoveToStage, onViewProfile, addToast }) => {
            const config = STAGE_CONFIG[title] || { color: 'bg-gray-100', badgeColor: 'bg-gray-200 text-gray-800', borderColor: 'border-gray-300', icon: 'fa-circle' };

            return (
                <div 
                    className="w-80 flex-shrink-0 flex flex-col h-full select-none"
                    onDragOver={onDragOver}
                    onDrop={(e) => onDrop(e, title)}
                >
                    {/* Column Header */}
                    <div className={`mb-3 flex items-center justify-between p-1`}>
                        <div className="flex items-center gap-2">
                            <div className={`w-2 h-8 rounded-full ${config.borderColor.replace('border', 'bg')}`}></div>
                            <div>
                                <h2 className="font-bold text-gray-700 text-sm">{title}</h2>
                                <p className="text-xs text-gray-400 font-medium">{influencers.length} influencers</p>
                            </div>
                        </div>
                        <div className={`w-6 h-6 rounded-full flex items-center justify-center text-xs ${config.badgeColor}`}>
                            <i className={`fas ${config.icon}`}></i>
                        </div>
                    </div>

                    {/* Column Body */}
                    <div className="bg-gray-100/50 rounded-2xl p-2 flex-1 overflow-y-auto custom-scrollbar border border-dashed border-gray-200 hover:border-gray-300 transition-colors min-h-[500px]">
                        <div className="space-y-3 pb-4">
                            {influencers.map(inf => (
                                <InfluencerCard 
                                    key={inf.id} 
                                    influencer={inf} 
                                    unreadCount={unreadCounts ? unreadCounts[inf.id] : 0}
                                    onDragStart={(e, inf) => {
                                        e.dataTransfer.setData("text/plain", inf.id);
                                        e.dataTransfer.setData("sourceStage", title);
                                        e.dataTransfer.effectAllowed = "move";
                                    }}
                                    onMoveToClosed={onMoveToClosed}
                                    onMoveToStage={onMoveToStage}
                                    onViewProfile={onViewProfile}
                                    addToast={addToast}
                                />
                            ))}
                            {influencers.length === 0 && (
                                <div className="h-32 flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-200 rounded-xl m-2 bg-white/30">
                                    <i className="fas fa-inbox text-2xl mb-2 opacity-50"></i>
                                    <span className="text-xs font-medium">No items yet</span>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            );
        };

        const Toast = ({ message, type, onClose }) => {
            useEffect(() => {
                const timer = setTimeout(() => {
                    onClose();
                }, 3000);
                return () => clearTimeout(timer);
            }, [onClose]);

            const styles = {
                success: 'bg-green-50 text-green-800 border-green-200',
                error: 'bg-red-50 text-red-800 border-red-200',
                info: 'bg-blue-50 text-blue-800 border-blue-200'
            };

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };

            return (
                <div className={`flex items-center w-80 p-4 mb-3 rounded-xl shadow-lg border ${styles[type] || styles.info} animate-slide-in-right backdrop-blur-sm`}>
                    <div className={`inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-600' : type === 'error' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'}`}>
                        <i className={`fas ${icons[type] || icons.info}`}></i>
                    </div>
                    <div className="ml-3 text-sm font-medium">{message}</div>
                    <button onClick={onClose} type="button" className="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-black/5 inline-flex h-8 w-8 text-gray-500 hover:text-gray-900 transition-colors">
                        <span className="sr-only">Close</span>
                        <i className="fas fa-times"></i>
                    </button>
                </div>
            );
        };

        const EMAIL_TEMPLATES = [
            {
                id: 'outreach',
                name: '📢 Collaboration Proposal',
                subject: 'Collaboration Invitation: [Brand Name] x {{name}}',
                body: `Hi {{name}},

I hope you're having a great week!

I'm reaching out from [Brand Name]. We've been following your content and love your style and engagement with your audience. We believe you'd be a perfect fit for our brand.

We'd love to send you some of our products to try out and potentially collaborate on a campaign.

Let me know if you're interested, and I can send over more details!

Best regards,
[Your Name]
[Brand Name] Team`
            },
            {
                id: 'followup',
                name: '👋 Follow-up',
                subject: 'Following up: Collaboration Opportunity',
                body: `Hi {{name}},

I just wanted to bump this to the top of your inbox in case you missed my previous email.

We're still very interested in working with you and would love to discuss a potential collaboration.

Looking forward to hearing from you!

Best,
[Your Name]`
            },
            {
                id: 'shipping',
                name: '📦 Product Shipped',
                subject: 'On the way! Your package has been shipped',
                body: `Hi {{name}},

Great news! Your package has been shipped and should arrive soon.

Here is the tracking number: [TRACKING_NUMBER]

Please let us know once you receive it. We can't wait to see what you create!

Best,
[Your Name]`
            },
            {
                id: 'content_reminder',
                name: '⏰ Content Reminder',
                subject: 'Reminder: Content for [Brand Name] Campaign',
                body: `Hi {{name}},

Hope you're doing well!

Just a friendly reminder that the content for our collaboration is due on [DATE]. 

Please let us know if you have any questions or need any assistance.

Best,
[Your Name]`
            },
            {
                id: 'thank_you',
                name: '🙏 Thank You',
                subject: 'Thank you for the amazing content!',
                body: `Hi {{name}},

We just saw your post and we love it! Thank you so much for the amazing content.

We'd love to work with you again in the future.

Best,
[Your Name]`
            }
        ];

        const AddInfluencerModal = ({ onClose, onSuccess }) => {
            const [platform, setPlatform] = useState('Instagram');
            const [username, setUsername] = useState('');
            const [fullName, setFullName] = useState('');
            const [email, setEmail] = useState('');
            const [stage, setStage] = useState('Contacted');
            const [campaignType, setCampaignType] = useState('Gifting');
            const [followers, setFollowers] = useState('');
            const [engRate, setEngRate] = useState('');
            const [location, setLocation] = useState('');
            const [niche, setNiche] = useState('');
            const [fee, setFee] = useState('');
            const [deliverables, setDeliverables] = useState('');
            const [notes, setNotes] = useState('');
            const [submitting, setSubmitting] = useState(false);
            const [errorMsg, setErrorMsg] = useState('');

            const platforms = [
                { name: 'Instagram', icon: 'fab fa-instagram' },
                { name: 'TikTok', icon: 'fab fa-tiktok' },
                { name: 'YouTube', icon: 'fab fa-youtube' },
                { name: 'Facebook', icon: 'fab fa-facebook' }
            ];

            const handleSubmit = async (e) => {
                e.preventDefault();
                const trimmedUser = username.trim().replace(/^@/, '');
                if (!trimmedUser) {
                    setErrorMsg('Please enter a username or handle.');
                    return;
                }

                setSubmitting(true);
                setErrorMsg('');

                try {
                    const formData = new FormData();
                    formData.append('platform', platform);
                    formData.append('username', trimmedUser);
                    formData.append('full_name', fullName.trim());
                    formData.append('email', email.trim());
                    formData.append('pipeline_status', stage);
                    formData.append('campaign_type', campaignType);
                    formData.append('followers', followers ? parseInt(followers.toString().replace(/,/g, ''), 10) || 0 : 0);
                    formData.append('eng_rate', engRate ? parseFloat(engRate) || 0 : 0);
                    formData.append('location', location.trim());
                    formData.append('niche', niche.trim());
                    formData.append('fee', fee ? parseFloat(fee) || 0 : 0);
                    formData.append('deliverables', deliverables.trim());
                    formData.append('notes', notes.trim());

                    const response = await axios.post('<?php echo base_url("influencer/add_pipeline_influencer_ajax"); ?>', formData);

                    if (response.data && response.data.status === 'success') {
                        onSuccess(response.data.influencer || { username: trimmedUser, pipeline_status: stage });
                    } else {
                        setErrorMsg(response.data?.message || 'Failed to add influencer.');
                    }
                } catch (err) {
                    console.error('Error adding influencer:', err);
                    setErrorMsg(err.response?.data?.message || err.message || 'Error communicating with server.');
                } finally {
                    setSubmitting(false);
                }
            };

            return (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" onClick={onClose}>
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[92vh] border border-gray-100 relative" onClick={e => e.stopPropagation()}>
                        
                        {/* Header */}
                        <div className="bg-gradient-to-r from-emerald-50 via-white to-emerald-50/30 p-6 border-b border-gray-100 flex items-center justify-between">
                            <div className="flex items-center gap-3.5">
                                <div className="w-11 h-11 rounded-2xl bg-brand-green/10 text-brand-deep flex items-center justify-center text-lg shadow-xs border border-emerald-200/50">
                                    <i className="fas fa-user-plus"></i>
                                </div>
                                <div>
                                    <h3 className="text-lg font-black text-gray-900 tracking-tight">Add Influencer to Pipeline</h3>
                                    <p className="text-xs text-gray-500 font-medium">Quickly onboard a new creator into your campaign workflow</p>
                                </div>
                            </div>
                            <button 
                                onClick={onClose} 
                                className="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors"
                            >
                                <i className="fas fa-times text-sm"></i>
                            </button>
                        </div>

                        {/* Form Body */}
                        <form onSubmit={handleSubmit} className="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-5">
                            {errorMsg && (
                                <div className="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2">
                                    <i className="fas fa-exclamation-triangle text-rose-500"></i>
                                    <span>{errorMsg}</span>
                                </div>
                            )}

                            {/* Platform Selector Buttons */}
                            <div>
                                <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-2">Platform</label>
                                <div className="grid grid-cols-4 gap-2.5">
                                    {platforms.map(p => (
                                        <button
                                            key={p.name}
                                            type="button"
                                            onClick={() => setPlatform(p.name)}
                                            className={`py-2.5 px-3 rounded-2xl border text-xs font-black flex items-center justify-center gap-2 transition-all ${
                                                platform === p.name 
                                                    ? 'border-brand-green bg-emerald-50/70 text-brand-deep ring-2 ring-brand-green/20 shadow-xs' 
                                                    : 'border-gray-200 bg-white hover:bg-gray-50 text-gray-600'
                                            }`}
                                        >
                                            <i className={p.icon}></i>
                                            <span>{p.name}</span>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Core Info Grid */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {/* Username */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Username <span className="text-rose-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <span className="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-gray-400">@</span>
                                        <input
                                            type="text"
                                            required
                                            value={username}
                                            onChange={(e) => setUsername(e.target.value)}
                                            placeholder="username (e.g. stylebyclaire)"
                                            className="w-full pl-8 pr-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                            autoFocus
                                        />
                                    </div>
                                </div>

                                {/* Full Name */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Full Name</label>
                                    <input
                                        type="text"
                                        value={fullName}
                                        onChange={(e) => setFullName(e.target.value)}
                                        placeholder="Creator real or display name"
                                        className="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                    />
                                </div>

                                {/* Email Address */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Email Address</label>
                                    <div className="relative">
                                        <span className="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs text-brand-green">
                                            <i className="fas fa-envelope"></i>
                                        </span>
                                        <input
                                            type="email"
                                            value={email}
                                            onChange={(e) => setEmail(e.target.value)}
                                            placeholder="creator@example.com"
                                            className="w-full pl-9 pr-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                        />
                                    </div>
                                </div>

                                {/* Pipeline Stage */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Pipeline Stage <span className="text-rose-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <select
                                            value={stage}
                                            onChange={(e) => setStage(e.target.value)}
                                            className="appearance-none w-full px-3.5 py-2.5 bg-emerald-50/50 border border-emerald-300 rounded-xl text-xs font-extrabold text-brand-deep focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 cursor-pointer"
                                        >
                                            {Object.keys(STAGE_CONFIG).map(s => (
                                                <option key={s} value={s}>{s}</option>
                                            ))}
                                        </select>
                                        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-brand-green">
                                            <i className="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                {/* Followers */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Followers</label>
                                    <input
                                        type="number"
                                        value={followers}
                                        onChange={(e) => setFollowers(e.target.value)}
                                        placeholder="e.g. 45000"
                                        className="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                    />
                                </div>

                                {/* Campaign Type */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Campaign Type</label>
                                    <div className="relative">
                                        <select
                                            value={campaignType}
                                            onChange={(e) => setCampaignType(e.target.value)}
                                            className="appearance-none w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 cursor-pointer"
                                        >
                                            <option value="Gifting">Gifting</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Affiliate">Affiliate</option>
                                        </select>
                                        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                            <i className="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                {/* Eng Rate & Fee */}
                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Eng. Rate (%)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={engRate}
                                        onChange={(e) => setEngRate(e.target.value)}
                                        placeholder="e.g. 4.8"
                                        className="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                    />
                                </div>

                                <div>
                                    <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Fee ($)</label>
                                    <input
                                        type="number"
                                        value={fee}
                                        onChange={(e) => setFee(e.target.value)}
                                        placeholder="e.g. 300"
                                        className="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                    />
                                </div>
                            </div>

                            {/* Deliverables */}
                            <div>
                                <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Deliverables</label>
                                <input
                                    type="text"
                                    value={deliverables}
                                    onChange={(e) => setDeliverables(e.target.value)}
                                    placeholder="e.g. 1x Instagram Reel + 3x Stories"
                                    className="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all"
                                />
                            </div>

                            {/* Notes */}
                            <div>
                                <label className="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Notes & Bio</label>
                                <textarea
                                    rows="2"
                                    value={notes}
                                    onChange={(e) => setNotes(e.target.value)}
                                    placeholder="Add any initial communication notes, niche tags, or requirements..."
                                    className="w-full px-3.5 py-2 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition-all resize-none"
                                ></textarea>
                            </div>

                            {/* Footer Buttons */}
                            <div className="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={onClose}
                                    disabled={submitting}
                                    className="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={submitting}
                                    className="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-[#0c5933] text-white font-extrabold text-xs transition-all shadow-md shadow-emerald-900/20 flex items-center gap-2 disabled:opacity-50"
                                >
                                    {submitting ? (
                                        <>
                                            <div className="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                            <span>Adding to Pipeline...</span>
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-plus text-xs"></i>
                                            <span>Add to Pipeline</span>
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            );
        };

        const App = () => {
            const [pipeline, setPipeline] = useState({
                'For Outreach': [],
                'Contacted': [],
                'Replied': [],
                'In - Progress': [],
                'Not Interested': []
            });
            const [loading, setLoading] = useState(true);
            const [search, setSearch] = useState('');
            const [selectedInfluencer, setSelectedInfluencer] = useState(null);
            const [showAddModal, setShowAddModal] = useState(false);
            const [toasts, setToasts] = useState([]);
            const [unreadCounts, setUnreadCounts] = useState({});
            const [viewMode, setViewMode] = useState('board'); // 'board' or 'list'
            const [selectedIds, setSelectedIds] = useState([]);

            const addToast = (message, type = 'info') => {
                const id = Date.now();
                setToasts(prev => [...prev, { id, message, type }]);
            };

            const removeToast = (id) => {
                setToasts(prev => prev.filter(toast => toast.id !== id));
            };

            const fetchUnreadCounts = async () => {
                try {
                    const response = await axios.get('<?php echo base_url("email/unread_counts"); ?>');
                    if (response.data.status === 'success') {
                        setUnreadCounts(response.data.data);
                    }
                } catch (error) {
                    console.error('Error fetching unread counts:', error);
                }
            };

            const checkNewEmails = async () => {
                 try {
                    const response = await axios.get('<?php echo base_url("email/check_new_emails"); ?>');
                    if (response.data.status === 'success' && response.data.new_count > 0) {
                        const senders = response.data.senders;
                        const msg = senders.length > 0 
                            ? `New emails from: ${senders.join(', ')}` 
                            : 'New emails received';
                        addToast(msg, 'success');
                        fetchUnreadCounts(); // Update badges immediately
                    }
                } catch (error) {
                    console.error('Error checking new emails:', error);
                }
            };

            useEffect(() => {
                fetchData();
                fetchUnreadCounts();
                
                const intervalCounts = setInterval(fetchUnreadCounts, 30000); // 30s
        const intervalCheck = setInterval(checkNewEmails, 60000); // 60s
                
                return () => {
                    clearInterval(intervalCounts);
                    clearInterval(intervalCheck);
                };
            }, []);

            const fetchData = async () => {
                try {
                    const response = await axios.get('<?php echo base_url("influencer/fetch_pipeline"); ?>');
                    setPipeline(response.data);
                    setLoading(false);
                } catch (error) {
                    console.error('Error fetching pipeline:', error);
                    setLoading(false);
                }
            };

            const handleDragOver = (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = "move";
            };

            const handleDrop = async (e, targetStage) => {
                e.preventDefault();
                const id = e.dataTransfer.getData("text/plain");
                const sourceStage = e.dataTransfer.getData("sourceStage");

                if (!id || sourceStage === targetStage) return;

                // Optimistic Update
                const movedInfluencer = pipeline[sourceStage].find(inf => inf.id == id);
                if (!movedInfluencer) return;

                const newPipeline = { ...pipeline };
                newPipeline[sourceStage] = newPipeline[sourceStage].filter(inf => inf.id != id);
                newPipeline[targetStage] = [...newPipeline[targetStage], { ...movedInfluencer, pipeline_status: targetStage }];
                
                setPipeline(newPipeline);

                // API Call
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('stage', targetStage);

                    const response = await axios.post('<?php echo base_url("influencer/update_pipeline_stage"); ?>', formData);
                    if (response.data.status !== 'success') {
                        throw new Error(response.data.message || 'Unknown server error');
                    }
                } catch (error) {
                    console.error('Error updating stage:', error);
                    addToast('Failed to update stage: ' + (error.message || error), 'error');
                    fetchData();
                }
            };

            const handleMoveToStage = async (id, targetStage) => {
                if (!id || !targetStage) return;

                const newPipeline = { ...pipeline };
                let sourceStage = '';
                let movedInfluencer = null;

                Object.keys(newPipeline).forEach(stage => {
                    const found = newPipeline[stage].find(inf => inf.id == id);
                    if (found) {
                        sourceStage = stage;
                        movedInfluencer = found;
                    }
                });

                if (!sourceStage || !movedInfluencer || sourceStage === targetStage) return;

                newPipeline[sourceStage] = newPipeline[sourceStage].filter(inf => inf.id != id);
                newPipeline[targetStage] = [...newPipeline[targetStage], { ...movedInfluencer, pipeline_status: targetStage }];
                setPipeline(newPipeline);

                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('stage', targetStage);

                    const response = await axios.post('<?php echo base_url("influencer/update_pipeline_stage"); ?>', formData);
                    if (response.data.status !== 'success') {
                        throw new Error(response.data.message || 'Unknown server error');
                    }
                    addToast(`Moved to ${targetStage}`, 'success');
                } catch (error) {
                    console.error('Error updating stage:', error);
                    addToast('Failed to update stage: ' + (error.message || error), 'error');
                    fetchData();
                }
            };

            const handleMoveToClosed = async (id) => {
                if (!confirm('Move this influencer to Closed Collaborations (Order Creation)?')) return;
                
                // Optimistic remove from Pipeline
                // Since "For Order Creation" is not a visible column here, we remove it.
                // We assume it's currently in "Completed" or similar.
                
                const newPipeline = { ...pipeline };
                let sourceStage = '';
                
                // Find where it is
                Object.keys(newPipeline).forEach(stage => {
                    const found = newPipeline[stage].find(inf => inf.id == id);
                    if (found) sourceStage = stage;
                });
                
                if (sourceStage) {
                    newPipeline[sourceStage] = newPipeline[sourceStage].filter(inf => inf.id != id);
                    setPipeline(newPipeline);
                }

                // API Call
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('stage', 'For Order Creation');

                    const response = await axios.post('<?php echo base_url("influencer/update_pipeline_stage"); ?>', formData);
                    if (response.data.status !== 'success') {
                        throw new Error(response.data.message || 'Unknown server error');
                    }
                    // Optionally show success message
                } catch (error) {
                    console.error('Error moving to closed:', error);
                    alert('Failed to move to closed: ' + (error.message || error));
                    fetchData();
                }
            };

            const handleUpdateInfluencer = async (id, data) => {
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    Object.keys(data).forEach(key => {
                         if (data[key] !== null && data[key] !== undefined) {
                            formData.append(key, data[key]);
                         }
                    });

                    const response = await axios.post('<?php echo base_url("influencer/update_detail_ajax"); ?>', formData);
                    if (response.data.status === 'success') {
                        // Refresh data to ensure consistency
                        fetchData();
                        // Update selected influencer local state to show changes immediately
                        setSelectedInfluencer(prev => ({ ...prev, ...data }));
                        return true;
                    } else {
                        alert('Failed to update: ' + (response.data.message || 'Unknown error'));
                        return false;
                    }
                } catch (error) {
                    console.error('Error updating:', error);
                    alert('Error updating influencer');
                    return false;
                }
            };

            const handleDeleteInfluencer = async (id) => {
                if (!confirm("Are you sure you want to remove this creator from the campaign?")) return;
                try {
                    const response = await axios.post(`<?php echo base_url("influencer/delete/"); ?>${id}`);
                    if (response.data.status === 'success') {
                        addToast("Creator deleted successfully!", "success");
                        fetchData();
                        setSelectedIds(prev => prev.filter(selectedId => selectedId !== id));
                    } else {
                        alert("Failed to delete creator.");
                    }
                } catch (error) {
                    console.error("Error deleting creator:", error);
                    alert("Error deleting creator");
                }
            };

            const handleBulkStageUpdate = async (targetStage) => {
                if (!targetStage || selectedIds.length === 0) return;
                try {
                    const formData = new FormData();
                    formData.append('ids', selectedIds.join(','));
                    formData.append('stage', targetStage);

                    const response = await axios.post('<?php echo base_url("influencer/bulk_add_pipeline"); ?>', formData);
                    if (response.data.status === 'success') {
                        addToast(`Bulk updated ${selectedIds.length} creators to ${targetStage}!`, 'success');
                        fetchData();
                        setSelectedIds([]);
                    } else {
                        alert('Failed to bulk update stage.');
                    }
                } catch (error) {
                    console.error('Error bulk updating stages:', error);
                    alert('Error performing bulk stage update.');
                }
            };

            const handleBulkDelete = async () => {
                if (selectedIds.length === 0) return;
                if (!confirm(`Are you sure you want to delete the ${selectedIds.length} selected creators?`)) return;
                try {
                    const formData = new FormData();
                    formData.append('ids', selectedIds.join(','));

                    const response = await axios.post('<?php echo base_url("influencer/delete_selected"); ?>', formData);
                    if (response.data.status === 'success') {
                        addToast(`Bulk deleted ${selectedIds.length} creators.`, 'success');
                        fetchData();
                        setSelectedIds([]);
                    } else {
                        alert('Failed to delete selected creators.');
                    }
                } catch (error) {
                    console.error('Error bulk deleting:', error);
                    alert('Error performing bulk deletion.');
                }
            };

            const filteredPipeline = () => {
                if (!search) return pipeline;
                const result = {};
                Object.keys(pipeline).forEach(stage => {
                    result[stage] = pipeline[stage].filter(inf => 
                        inf.username.toLowerCase().includes(search.toLowerCase()) ||
                        (inf.notes && inf.notes.toLowerCase().includes(search.toLowerCase()))
                    );
                });
                return result;
            };

            if (loading) {
                return (
                    <div className="flex justify-center items-center h-full">
                        <div className="flex flex-col items-center">
                             <div className="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-brand-green mb-4"></div>
                             <p className="text-gray-500 text-sm font-medium">Loading Pipeline...</p>
                        </div>
                    </div>
                );
            }

            const displayData = filteredPipeline();
            const totalInfluencers = Object.values(displayData).flat().length;

            return (
                <div className={`h-full flex flex-col ${viewMode === 'board' ? 'min-w-max' : 'w-full'}`}>
                    {/* Action Header */}
                    <div className="flex justify-between items-center mb-8 sticky left-0 px-1 pt-2">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-800 tracking-tight">
                                {viewMode === 'board' ? 'Pipeline Board' : 'Pipeline List'}
                            </h1>
                            <p className="text-sm text-gray-400 mt-1 font-medium">Manage your influencer outreach campaign workflow</p>
                        </div>
                        
                        <div className="flex items-center gap-4">
                            {/* View Switcher Toggler */}
                            <div className="flex bg-gray-100 p-1 rounded-full border border-gray-200 shadow-xs mr-2">
                                <button 
                                    onClick={() => setViewMode('board')}
                                    className={`px-4 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1.5 transition-all duration-200 ${viewMode === 'board' ? 'bg-brand-deep text-white shadow-xs' : 'text-gray-500 hover:text-gray-800'}`}
                                    title="Kanban Board"
                                >
                                    <i className="fas fa-th-large"></i>
                                    <span>Board</span>
                                </button>
                                <button 
                                    onClick={() => setViewMode('list')}
                                    className={`px-4 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1.5 transition-all duration-200 ${viewMode === 'list' ? 'bg-brand-deep text-white shadow-xs' : 'text-gray-500 hover:text-gray-800'}`}
                                    title="Spreadsheet List"
                                >
                                    <i className="fas fa-list"></i>
                                    <span>List</span>
                                </button>
                            </div>

                            <div className="group flex items-center bg-white border border-gray-200 rounded-full px-4 py-2 shadow-sm hover:shadow-md focus-within:ring-2 focus-within:ring-brand-green/20 focus-within:border-brand-green transition-all duration-300 w-72">
                                <i className="fas fa-search text-gray-400 group-hover:text-brand-green transition-colors mr-3"></i>
                                <input 
                                    type="text" 
                                    placeholder="Search influencers..." 
                                    className="bg-transparent border-none text-sm focus:outline-none w-full text-gray-700 placeholder-gray-400"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                {search && (
                                    <button onClick={() => setSearch('')} className="text-gray-400 hover:text-red-500 transition-colors ml-2">
                                        <i className="fas fa-times-circle"></i>
                                    </button>
                                )}
                            </div>
                            
                            <button 
                                onClick={() => setShowAddModal(true)}
                                className="bg-brand-deep text-white hover:bg-[#0c5933] px-5 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-green-900/20 transition-all hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer"
                                title="Add New Influencer to Pipeline"
                            >
                                <i className="fas fa-plus"></i>
                                <span>Add New</span>
                            </button>
                        </div>
                    </div>
                    
                    {viewMode === 'board' ? (
                        /* Kanban Board */
                        <div className="flex gap-6 items-start pb-8 flex-1 pl-1">
                            {Object.keys(pipeline).map(stage => (
                                <PipelineColumn 
                                    key={stage} 
                                    title={stage} 
                                    influencers={displayData[stage]} 
                                    unreadCounts={unreadCounts}
                                    onDrop={handleDrop}
                                    onDragOver={handleDragOver}
                                    onMoveToClosed={handleMoveToClosed}
                                    onMoveToStage={handleMoveToStage}
                                    onViewProfile={setSelectedInfluencer}
                                    addToast={addToast}
                                />
                            ))}
                        </div>
                    ) : (
                        /* Spreadsheet List View */
                        <SpreadsheetListView 
                            displayData={displayData}
                            selectedIds={selectedIds}
                            setSelectedIds={setSelectedIds}
                            onUpdateStage={handleMoveToStage}
                            onUpdateDetails={handleUpdateInfluencer}
                            onViewProfile={setSelectedInfluencer}
                            onDelete={handleDeleteInfluencer}
                        />
                    )}

                    {/* Profile Modal */}
                    <ProfileModal 
                        influencer={selectedInfluencer} 
                        onClose={() => setSelectedInfluencer(null)} 
                        onUpdate={handleUpdateInfluencer}
                    />

                    {/* Add Influencer to Pipeline Modal */}
                    {showAddModal && (
                        <AddInfluencerModal 
                            onClose={() => setShowAddModal(false)}
                            onSuccess={(newInf) => {
                                setShowAddModal(false);
                                addToast(`Successfully added @${newInf.username || 'creator'} to ${newInf.pipeline_status || 'pipeline'}!`, 'success');
                                fetchData();
                            }}
                        />
                    )}

                    {/* Bulk Action Floating Panel */}
                    {selectedIds.length > 0 && (
                        <div className="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-[#111827] text-white px-6 py-4 rounded-3xl shadow-2xl flex items-center gap-6 z-50 border border-gray-800 animate-slide-up" style={{ minWidth: '550px' }}>
                            <div className="flex items-center gap-2">
                                <span className="w-5 h-5 rounded-full bg-brand-green text-white flex items-center justify-center text-[10px] font-black">{selectedIds.length}</span>
                                <span className="text-xs font-bold text-gray-300">creators selected</span>
                            </div>

                            <div className="h-6 w-px bg-gray-700"></div>

                            <div className="flex items-center gap-3">
                                <span className="text-xs font-bold text-gray-400">Bulk Actions:</span>
                                <select 
                                    onChange={(e) => {
                                        if (e.target.value) {
                                            handleBulkStageUpdate(e.target.value);
                                            e.target.value = '';
                                        }
                                    }}
                                    className="bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 cursor-pointer"
                                >
                                    <option value="">Move to stage...</option>
                                    <option value="For Outreach">For Outreach</option>
                                    <option value="Contacted">Contacted</option>
                                    <option value="Replied">Replied</option>
                                    <option value="In - Progress">In Progress</option>
                                    <option value="Not Interested">Not Interested</option>
                                    <option value="For Order Creation">For Order Creation</option>
                                    <option value="In-Transit">In-Transit</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Posted">Posted</option>
                                    <option value="Campaign Completed">Campaign Completed</option>
                                </select>

                                <button 
                                    onClick={handleBulkDelete}
                                    className="bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/20 px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5"
                                >
                                    <i className="fas fa-trash-alt"></i>
                                    <span>Delete</span>
                                </button>
                            </div>

                            <div className="h-6 w-px bg-gray-700"></div>

                            <button 
                                onClick={() => setSelectedIds([])}
                                className="text-xs text-gray-400 hover:text-white font-extrabold transition-colors"
                            >
                                Deselect
                            </button>
                        </div>
                    )}

                    {/* Toast Container */}
                    <div className="fixed top-20 right-4 z-[60] flex flex-col gap-2 pointer-events-none">
                        {toasts.map(toast => (
                            <div key={toast.id} className="pointer-events-auto">
                                <Toast 
                                    message={toast.message} 
                                    type={toast.type} 
                                    onClose={() => removeToast(toast.id)} 
                                />
                            </div>
                        ))}
                    </div>
                </div>
            );
        };

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>

    <script>
        const NOTIF_BASE_URL = <?php echo json_encode(base_url()); ?>;

        function escapeHtml(str) {
            return String(str || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function formatNotifTime(ts) {
            if (!ts) return '';
            const d = new Date(ts.replace(' ', 'T'));
            if (isNaN(d.getTime())) return ts;
            return d.toLocaleString();
        }

        // ── Notification helpers ─────────────────────────────────────────────────
        function _notifTimeAgo(dateStr) {
            const now  = Date.now();
            const then = new Date(dateStr.replace(' ', 'T')).getTime();
            if (isNaN(then)) return dateStr;
            const diff = Math.max(0, Math.floor((now - then) / 1000));
            if (diff <  60)    return 'Just now';
            if (diff < 3600)   return Math.floor(diff / 60)   + ' min ago';
            if (diff < 86400)  return Math.floor(diff / 3600) + ' hr ago';
            if (diff < 172800) return 'Yesterday';
            return Math.floor(diff / 86400) + ' days ago';
        }

        const _NOTIF_ICON = {
            login:      { icon: 'fa-sign-in-alt',          bg: 'bg-blue-100',   fg: 'text-blue-600'   },
            success:    { icon: 'fa-check-circle',          bg: 'bg-green-100',  fg: 'text-green-600'  },
            warning:    { icon: 'fa-exclamation-triangle',  bg: 'bg-yellow-100', fg: 'text-yellow-600' },
            error:      { icon: 'fa-times-circle',          bg: 'bg-red-100',    fg: 'text-red-600'    },
            influencer: { icon: 'fa-user-plus',             bg: 'bg-indigo-100', fg: 'text-indigo-600' },
            campaign:   { icon: 'fa-handshake',             bg: 'bg-purple-100', fg: 'text-purple-600' },
            task:       { icon: 'fa-tasks',                 bg: 'bg-teal-100',   fg: 'text-teal-600'   },
            info:       { icon: 'fa-bell',                  bg: 'bg-gray-100',   fg: 'text-gray-500'   },
        };

        function _notifIcon(n) {
            const m  = _NOTIF_ICON[n.type] || _NOTIF_ICON.info;
            const ic = (n.icon && n.icon !== 'fa-bell') ? n.icon : m.icon;
            return `<span class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center ${m.bg}"><i class="fas ${ic} text-sm ${m.fg}"></i></span>`;
        }

        async function refreshNotifSummary() {
            try {
                const res = await fetch(NOTIF_BASE_URL + 'notifications/summary', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                const badge = document.getElementById('notif-badge');
                if (!badge) return;
                const count = data.unread || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        function renderNotifList(items) {
            const wrap = document.getElementById('notif-list');
            if (!wrap) return;
            if (!items || items.length === 0) {
                wrap.innerHTML =
                    '<div class="flex flex-col items-center justify-center py-12 text-gray-300">' +
                    '<i class="far fa-bell-slash text-4xl mb-3"></i>' +
                    '<p class="text-sm font-medium text-gray-400">No notifications yet</p>' +
                    '<p class="text-xs text-gray-300 mt-1">You\'re all caught up!</p>' +
                    '</div>';
                return;
            }
            wrap.innerHTML = items.map(n => {
                const unreadBar = n.is_read ? '' : '<span class="absolute left-0 top-0 h-full w-1 bg-indigo-500 rounded-l-md"></span>';
                const rowBg    = n.is_read ? '' : 'bg-indigo-50/30';
                const iconHtml = _notifIcon(n);
                const timeStr  = _notifTimeAgo(n.created_at || '');
                const title    = escapeHtml(n.title   || 'Notification');
                const msg      = escapeHtml(n.message || '');
                const url      = n.url || '';
                return (
                    `<div class="relative flex items-start gap-0 border-b border-gray-50 ${rowBg} hover:bg-gray-50 transition">` +
                    unreadBar +
                    `<div onclick="openNotif(${n.id},'${encodeURIComponent(url)}')" class="flex items-start gap-3 flex-1 min-w-0 px-5 py-3 cursor-pointer">` +
                    iconHtml +
                    `<div class="flex-1 min-w-0">` +
                    `<p class="text-sm font-semibold text-gray-800 truncate">${title}</p>` +
                    (msg ? `<p class="text-xs text-gray-500 leading-snug line-clamp-2 mt-0.5">${msg}</p>` : '') +
                    `<p class="text-[11px] text-gray-400 mt-1">${timeStr}</p>` +
                    `</div></div>` +
                    `<button onclick="deleteNotif(${n.id})" class="flex-shrink-0 self-start mt-2 mr-3 text-gray-300 hover:text-red-400 transition text-lg leading-none" title="Dismiss">&times;</button>` +
                    `</div>`
                );
            }).join('');
        }

        async function loadNotifList() {
            try {
                const res = await fetch(NOTIF_BASE_URL + 'notifications/list?limit=15', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                renderNotifList(data.items || []);
                const badge = document.getElementById('notif-badge');
                if (!badge) return;
                const count = data.unread || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        function toggleNotifMenu() {
            const menu = document.getElementById('notif-menu');
            if (!menu) return;
            const opening = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (opening) loadNotifList();
        }

        async function markAllNotifRead() {
            try {
                await fetch(NOTIF_BASE_URL + 'notifications/mark_read', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'all=1'
                });
            } catch (e) {}
            await loadNotifList();
            await refreshNotifSummary();
        }

        async function deleteNotif(id) {
            try {
                await fetch(NOTIF_BASE_URL + 'notifications/delete', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                });
            } catch (e) {}
            loadNotifList();
        }

        function openNotif(id, encodedUrl) {
            fetch(NOTIF_BASE_URL + 'notifications/mark_read', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            }).then(() => refreshNotifSummary()).catch(() => {});
            const url = decodeURIComponent(encodedUrl);
            if (url && url !== '#') window.location.href = url;
        }

        // Unified header scripts are loaded from partials/header.php
        document.addEventListener('DOMContentLoaded', function() {
            refreshNotifSummary();
        });
    </script>
</body>
</html>
