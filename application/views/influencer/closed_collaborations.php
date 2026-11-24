<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Closed Collaborations - Instroom</title>
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
        <?php $this->load->view('partials/header', ['parent' => 'Campaigns', 'child' => 'Closed']); ?>

        <!-- Main Content (React Root) -->
        <main class="flex-1 overflow-x-auto bg-[#F8FAFC] p-6 md:p-8 scroll-smooth relative" id="root">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-green-50/50 to-transparent -z-10"></div>
            
            <!-- React will render here -->
            <div class="flex justify-center items-center h-full">
                <div class="flex flex-col items-center">
                     <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-100 border-t-brand-green mb-4"></div>
                     <p class="text-gray-500 text-sm font-medium font-sans">Loading Closed Collaborations...</p>
                </div>
            </div>
        </main>
    </div>

    <!-- React Application -->
    <script type="text/babel">
        const { useState, useEffect } = React;

        const STAGE_CONFIG = {
            'For Order Creation': { 
                color: 'bg-teal-50', 
                badgeColor: 'bg-teal-100 text-teal-700',
                borderColor: 'border-teal-300',
                icon: 'fa-cart-plus',
                description: 'Ready for Shopify Order'
            },
            'In-Transit': { 
                color: 'bg-amber-50', 
                badgeColor: 'bg-amber-100 text-amber-700',
                borderColor: 'border-amber-300',
                icon: 'fa-truck',
                description: 'Shipped & Tracking Active'
            },
            'Delivered': { 
                color: 'bg-cyan-50', 
                badgeColor: 'bg-cyan-100 text-cyan-700',
                borderColor: 'border-cyan-300',
                icon: 'fa-box-open',
                description: 'Product Received'
            },
            'Posted': { 
                color: 'bg-green-50', 
                badgeColor: 'bg-green-100 text-green-700',
                borderColor: 'border-green-300',
                icon: 'fa-share-square',
                description: 'Content Live'
            },
            'Campaign Completed': { 
                color: 'bg-purple-50', 
                badgeColor: 'bg-purple-100 text-purple-700',
                borderColor: 'border-purple-300',
                icon: 'fa-flag-checkered',
                description: 'All Deliverables Met'
            }
        };

        const ShopifyBadge = ({ status }) => {
            if (!status) return null;
            const config = {
                'unfulfilled': { color: 'bg-yellow-100 text-yellow-700', icon: 'fa-clock' },
                'fulfilled': { color: 'bg-blue-100 text-blue-700', icon: 'fa-truck' },
                'delivered': { color: 'bg-green-100 text-green-700', icon: 'fa-check' }
            }[status.toLowerCase()] || { color: 'bg-gray-100 text-gray-600', icon: 'fa-question' };

            return (
                <div className={`flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase ${config.color}`}>
                    <i className={`fas ${config.icon}`}></i> {status}
                </div>
            );
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
            const [editingField, setEditingField] = useState(null);
            const [editValue, setEditValue] = useState('');
            const [saving, setSaving] = useState(false);
            const [showEmailComposer, setShowEmailComposer] = useState(false);
            const [emailSubject, setEmailSubject] = useState('');
            const [emailBody, setEmailBody] = useState('');
            const [emailHistory, setEmailHistory] = useState([]);
            const [sendingEmail, setSendingEmail] = useState(false);
            const [syncingEmail, setSyncingEmail] = useState(false);
            const messagesEndRef = React.useRef(null);
            const [showTemplates, setShowTemplates] = useState(false);

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

            useEffect(() => {
                if (influencer) {
                    setNoteContent(influencer.notes || '');
                    setEditingField(null);
                    setEditValue('');
                }
            }, [influencer]);

            if (!influencer) return null;

            const copyToClipboard = (text) => {
                navigator.clipboard.writeText(text);
            };

            const formatNumber = (num) => {
                if (num === null || num === undefined || num === '') return '0';
                const val = parseFloat(num);
                if (isNaN(val) || val === 0) return '0';
                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                if (val >= 1000) return (val / 1000).toFixed(1) + 'K';
                return Math.round(val).toLocaleString();
            };

            const handleNoteBlur = async () => {
                if (noteContent !== influencer.notes) {
                    setSaving(true);
                    await onUpdate(influencer.id, { notes: noteContent });
                    setSaving(false);
                }
            };

            const handleStatusChange = async (e) => {
                const newStatus = e.target.value;
                setSaving(true);
                await onUpdate(influencer.id, { pipeline_status: newStatus });
                setSaving(false);
            };

            const handleCampaignTypeChange = async (e) => {
                 const newType = e.target.value;
                 setSaving(true);
                 await onUpdate(influencer.id, { campaign_type: newType });
                 setSaving(false);
            };

            const startEditing = (field, value) => {
                setEditingField(field);
                setEditValue(value || '');
            };

            const saveEditing = async () => {
                if (!editingField) return;
                setSaving(true);
                const success = await onUpdate(influencer.id, { [editingField]: editValue });
                if (success) {
                    setEditingField(null);
                }
                setSaving(false);
            };

            const renderEditableRow = (label, field, value, icon = null, type = 'text') => {
                const isEditing = editingField === field;
                return (
                    <div className={`group flex justify-between items-start border-b border-gray-100 pb-2 ${type === 'textarea' ? 'h-auto py-2' : 'h-10 items-center'}`}>
                        <div className="flex-1 flex items-start">
                            <div className={`text-xs font-bold text-gray-900 w-24 ${type === 'textarea' ? 'mt-2' : ''}`}>{label}</div>
                            {isEditing ? (
                                <div className="flex items-start gap-2 flex-1">
                                    {type === 'textarea' ? (
                                        <textarea
                                            value={editValue}
                                            onChange={(e) => setEditValue(e.target.value)}
                                            className="flex-1 border border-brand-green rounded px-2 py-1 text-xs focus:outline-none"
                                            rows="3"
                                            autoFocus
                                        ></textarea>
                                    ) : (
                                        <input 
                                            type={type} 
                                            value={editValue} 
                                            onChange={(e) => setEditValue(e.target.value)}
                                            className="flex-1 border border-brand-green rounded px-2 py-1 text-xs focus:outline-none"
                                            autoFocus
                                            onKeyDown={(e) => {
                                                if (e.key === 'Enter') saveEditing();
                                                if (e.key === 'Escape') setEditingField(null);
                                            }}
                                        />
                                    )}
                                    <div className={`flex gap-1 ${type === 'textarea' ? 'mt-1' : ''}`}>
                                        <button onClick={saveEditing} className="text-green-600 hover:text-green-800">
                                            <i className="fas fa-check"></i>
                                        </button>
                                        <button onClick={() => setEditingField(null)} className="text-red-500 hover:text-red-700">
                                            <i className="fas fa-times"></i>
                                        </button>
                                    </div>


                                </div>
                            ) : (
                                <div className="text-sm text-gray-600 flex items-center gap-2 break-all">
                                    <span>{(value !== null && value !== undefined && value !== '') ? value : '-'}</span>
                                    {icon}
                                </div>
                            )}
                        </div>
                        {!isEditing && (
                            <button onClick={() => startEditing(field, value)} className={`text-brand-blue opacity-0 group-hover:opacity-100 transition-opacity ${type === 'textarea' ? 'mt-1' : ''}`}>
                                <i className="far fa-edit"></i>
                            </button>
                        )}
                    </div>
                );
            };

            return (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" onClick={onClose}>
                    <div className="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]" onClick={e => e.stopPropagation()}>
                        
                        {/* Header Section */}
                        <div className="p-6 pb-0">
                            <div className="flex justify-between items-start mb-6">
                                <div className="flex gap-4">
                                    {/* Avatar */}
                                    <div className="relative">
                                        <div className="w-16 h-16 rounded-full bg-brand-green flex items-center justify-center text-white text-2xl font-bold">
                                            {influencer.username ? influencer.username.charAt(0).toUpperCase() : 'U'}
                                        </div>
                                        <div className="absolute -bottom-1 -right-1 bg-white rounded-full p-1">
                                            <PlatformIcon platform={influencer.platform} />
                                        </div>
                                    </div>

                                    {/* Info & Dropdowns */}
                                    <div>
                                        <div className="flex items-center gap-4 mb-2">
                                            <h2 className="text-xl font-bold text-gray-900">
                                                {influencer.full_name || influencer.username || 'Unknown Influencer'}
                                            </h2>
                                            
                                            {/* Pipeline Status Dropdown */}
                                            <div className="flex flex-col">
                                                <label className="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Pipeline status</label>
                                                <div className="relative">
                                                    <select 
                                                        className="appearance-none bg-white border border-brand-green text-brand-deep text-xs font-bold rounded px-3 py-1 pr-8 focus:outline-none focus:ring-1 focus:ring-brand-green cursor-pointer"
                                                        value={influencer.pipeline_status || 'For Order Creation'}
                                                        onChange={handleStatusChange}
                                                        disabled={saving}
                                                    >
                                                        {Object.keys(STAGE_CONFIG).map(stage => (
                                                            <option key={stage} value={stage}>{stage}</option>
                                                        ))}
                                                    </select>
                                                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-brand-green">
                                                        <i className="fas fa-chevron-down text-[10px]"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Campaign Type Dropdown */}
                                            <div className="flex flex-col">
                                                <label className="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Campaign type</label>
                                                <div className="relative">
                                                    <select 
                                                        className="appearance-none bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded px-3 py-1 pr-8 focus:outline-none focus:ring-1 focus:ring-brand-green cursor-pointer"
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
                                                        <i className="fas fa-chevron-down text-[10px]"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Email Row */}
                                        <div className="flex items-center gap-2 text-gray-500 text-sm">
                                            <span>{influencer.email || 'No email provided'}</span>
                                            {influencer.email && (
                                                <button onClick={() => copyToClipboard(influencer.email)} className="hover:text-brand-green transition-colors" title="Copy Email">
                                                    <i className="far fa-copy"></i>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Close Button */}
                                <button onClick={onClose} className="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i className="fas fa-times text-xl"></i>
                                </button>
                            </div>

                            {/* Tabs */}
                            <div className="flex gap-6 border-b border-gray-200">
                                <button 
                                    onClick={() => setActiveTab('basic')}
                                    className={`pb-3 text-sm font-bold border-b-2 transition-colors ${activeTab === 'basic' ? 'border-brand-dark text-brand-dark' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-info-circle mr-2"></i>Basic Information
                                </button>
                                <button 
                                    onClick={() => setActiveTab('orders')}
                                    className={`pb-3 text-sm font-bold border-b-2 transition-colors ${activeTab === 'orders' ? 'border-brand-dark text-brand-dark' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-shopping-cart mr-2"></i>Order Details
                                </button>
                                <button 
                                    onClick={() => setActiveTab('insight')}
                                    className={`pb-3 text-sm font-bold border-b-2 transition-colors ${activeTab === 'insight' ? 'border-brand-dark text-brand-dark' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-chart-bar mr-2"></i>Post Insight
                                </button>
                                <button 
                                    onClick={() => setActiveTab('stats')}
                                    className={`pb-3 text-sm font-bold border-b-2 transition-colors ${activeTab === 'stats' ? 'border-brand-dark text-brand-dark' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
                                >
                                    <i className="fas fa-chart-line mr-2"></i>Statistics
                                </button>
                            </div>
                        </div>

                        {/* Content Area */}
                        <div className="p-6 overflow-y-auto custom-scrollbar flex-1">
                            {activeTab === 'basic' && (
                                <div className="space-y-6">
                                    {/* Action Buttons */}
                                    <div className="flex flex-wrap gap-3">
                                        <a 
                                            href={influencer.platform === 'Instagram' ? `https://instagram.com/${influencer.username}` : '#'} 
                                            target="_blank" 
                                            className="bg-gradient-to-r from-pink-500 to-rose-500 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:opacity-90 transition-opacity"
                                        >
                                            <i className={`fab fa-${influencer.platform ? influencer.platform.toLowerCase() : 'instagram'}`}></i> 
                                            {influencer.username}
                                        </a>
                                        <button 
                                            onClick={() => {
                                                setShowEmailComposer(true);
                                                fetch('<?= base_url('email/mark_read') ?>/' + influencer.id);
                                            }} 
                                            className="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-600 transition-colors"
                                        >
                                            Send Email
                                        </button>
                                        <button className="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-600 transition-colors">
                                            Send DM
                                        </button>
                                        <button className="bg-brand-dark text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition-colors">
                                            Follow up
                                        </button>
                                    </div>

                                    {/* Metrics Row */}
                                    <div className="bg-blue-50/50 rounded-xl p-6 relative">
                                        <div className="grid grid-cols-4 gap-8">
                                            <div>
                                                <div className="text-gray-500 text-xs font-bold uppercase mb-1 flex items-center gap-1">
                                                    <i className="fas fa-users text-gray-400"></i> Followers
                                                </div>
                                                <div className="text-2xl font-bold text-gray-900">{formatNumber(influencer.followers)}</div>
                                            </div>
                                            <div>
                                                <div className="text-gray-500 text-xs font-bold uppercase mb-1 flex items-center gap-1">
                                                    <i className="fas fa-bolt text-gray-400"></i> Eng. rate
                                                </div>
                                                <div className="text-2xl font-bold text-gray-900">{influencer.eng_rate || 0} %</div>
                                            </div>
                                            <div>
                                                <div className="text-gray-500 text-xs font-bold uppercase mb-1 flex items-center gap-1">
                                                    <i className="fas fa-play-circle text-brand-blue"></i> Avg. Views
                                                </div>
                                                <div className="text-2xl font-bold text-gray-900">{formatNumber(influencer.avg_video_views || 0)}</div>
                                            </div>
                                            <div>
                                                <div className="text-gray-500 text-xs font-bold uppercase mb-1 flex items-center gap-1">
                                                    <i className="fas fa-bullhorn text-brand-blue"></i> GMV
                                                </div>
                                                <div className="text-2xl font-bold text-gray-900">$ {formatNumber(influencer.gmv || 0)}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Details Grid */}
                                    <div className="grid grid-cols-2 gap-x-12 gap-y-6">
                                        {renderEditableRow('Email', 'email', influencer.email)}
                                        {renderEditableRow('Last Name', 'last_name', influencer.last_name)}
                                        {renderEditableRow('First Name', 'first_name', influencer.first_name)}
                                        {renderEditableRow('Username', 'username', influencer.username)}
                                        {renderEditableRow('Platform', 'platform', influencer.platform, <PlatformIcon platform={influencer.platform} />)}
                                        {renderEditableRow('Niche', 'niche', influencer.niche)}
                                        {renderEditableRow('Location', 'location', influencer.location)}
                                    </div>

                                    {/* Notes */}
                                    <div>
                                        <h3 className="text-sm font-bold text-brand-dark mb-2">Notes {saving && <span className="text-xs text-gray-400 font-normal ml-2">Saving...</span>}</h3>
                                        <textarea 
                                            className="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-all resize-none"
                                            rows="4"
                                            placeholder="Add notes about this influencer..."
                                            value={noteContent}
                                            onChange={(e) => setNoteContent(e.target.value)}
                                            onBlur={handleNoteBlur}
                                        ></textarea>
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

        const InfluencerCard = ({ influencer, onDragStart, onViewProfile }) => {
            const [isDragging, setIsDragging] = useState(false);

            const handleDragStart = (e) => {
                setIsDragging(true);
                onDragStart(e, influencer);
            };

            const handleDragEnd = () => {
                setIsDragging(false);
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
                                <div className="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5 shadow-sm">
                                    <div className="w-4 h-4 flex items-center justify-center rounded-full bg-gray-50 text-[10px]">
                                        <PlatformIcon platform={influencer.platform} />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 className="font-bold text-sm text-gray-800 leading-tight group-hover:text-brand-deep transition-colors cursor-pointer" onClick={() => onViewProfile(influencer)}>
                                    {influencer.username}
                                </h3>
                                <p className="text-[10px] text-gray-500 font-medium uppercase tracking-wide mt-0.5">
                                    {influencer.platform || 'Unknown'}
                                </p>
                            </div>
                        </div>
                        <button className="text-gray-300 hover:text-gray-500 transition-colors p-1 rounded hover:bg-gray-50">
                            <i className="fas fa-ellipsis-v text-xs"></i>
                        </button>
                    </div>
                    
                    <div className="flex items-center gap-2 mb-3">
                        <div className="px-2 py-1 bg-gray-50 rounded-md border border-gray-100 flex items-center gap-1.5" title="Followers">
                            <i className="fas fa-users text-[10px] text-gray-400"></i>
                            <span className="text-xs font-semibold text-gray-600">{influencer.followers || '0'}</span>
                        </div>
                        {influencer.order_number && (
                            <div className="px-2 py-1 bg-gray-50 rounded-md border border-gray-100 flex items-center gap-1.5" title="Shopify Order">
                                <i className="fab fa-shopify text-[10px] text-green-600"></i>
                                <span className="text-xs font-semibold text-gray-600">#{influencer.order_number}</span>
                            </div>
                        )}
                        <ShopifyBadge status={influencer.shipping_status || (influencer.order_number ? 'Unfulfilled' : null)} />
                    </div>
                    
                    {influencer.notes && (
                        <div className="text-xs text-gray-500 bg-yellow-50/50 p-2 rounded-lg border border-yellow-100/50 line-clamp-2 italic">
                            "{influencer.notes}"
                        </div>
                    )}

                    <div className="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <span className="text-[10px] text-gray-400">Updated today</span>
                        <button 
                            onClick={() => onViewProfile(influencer)}
                            className="text-xs font-medium text-brand-green hover:underline"
                        >
                            View Details
                        </button>
                    </div>
                </div>
            );
        };

        const PipelineColumn = ({ title, influencers, onDrop, onDragOver, onViewProfile }) => {
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
                                    onDragStart={(e, inf) => {
                                        e.dataTransfer.setData("text/plain", inf.id);
                                        e.dataTransfer.setData("sourceStage", title);
                                        e.dataTransfer.effectAllowed = "move";
                                    }}
                                    onViewProfile={onViewProfile}
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

        const App = () => {
            const [pipeline, setPipeline] = useState({
                'For Order Creation': [],
                'In-Transit': [],
                'Delivered': [],
                'Posted': [],
                'Campaign Completed': []
            });
            const [loading, setLoading] = useState(true);
            const [search, setSearch] = useState('');
            const [selectedInfluencer, setSelectedInfluencer] = useState(null);
            const [toasts, setToasts] = useState([]);

            const addToast = (message, type = 'info') => {
                const id = Date.now();
                setToasts(prev => [...prev, { id, message, type }]);
            };

            const removeToast = (id) => {
                setToasts(prev => prev.filter(toast => toast.id !== id));
            };

            useEffect(() => {
                fetchData();
            }, []);

            const fetchData = async () => {
                try {
                    const response = await axios.get('<?php echo base_url("influencer/fetch_closed_collaborations"); ?>');
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
                    // Revert on error - simplified for now, ideally revert state
                    alert('Failed to update stage: ' + (error.message || error));
                    fetchData();
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
                        addToast('Influencer updated successfully', 'success');
                        return true;
                    } else {
                        addToast('Failed to update: ' + (response.data.message || 'Unknown error'), 'error');
                        return false;
                    }
                } catch (error) {
                    console.error('Error updating:', error);
                    addToast('Error updating influencer', 'error');
                    return false;
                }
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
                <div className="min-w-max h-full flex flex-col">
                    {/* Action Header */}
                    <div className="flex justify-between items-center mb-8 sticky left-0 px-1 pt-2">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-800 tracking-tight">Closed Collaborations</h1>
                            <p className="text-sm text-gray-400 mt-1 font-medium">Manage and track your completed campaigns</p>
                        </div>
                        
                        <div className="flex items-center gap-4">
                            <div className="group flex items-center bg-white border border-gray-200 rounded-full px-4 py-2 shadow-sm hover:shadow-md focus-within:ring-2 focus-within:ring-brand-green/20 focus-within:border-brand-green transition-all duration-300 w-72">
                                <i className="fas fa-search text-gray-400 group-hover:text-brand-green transition-colors mr-3"></i>
                                <input 
                                    type="text" 
                                    placeholder="Search by name or platform..." 
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
                        </div>
                    </div>
                    
                    {/* Kanban Board */}
                    <div className="flex gap-6 items-start pb-8 flex-1 pl-1">
                        {Object.keys(pipeline).map(stage => (
                            <PipelineColumn 
                                key={stage} 
                                title={stage} 
                                influencers={displayData[stage]} 
                                onDrop={handleDrop}
                                onDragOver={handleDragOver}
                                onViewProfile={setSelectedInfluencer}
                            />
                        ))}
                    </div>

                    {/* Profile Modal */}
                    <ProfileModal 
                        influencer={selectedInfluencer} 
                        onClose={() => setSelectedInfluencer(null)} 
                        onUpdate={handleUpdateInfluencer}
                    />
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
