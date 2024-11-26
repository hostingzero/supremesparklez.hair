<div class="bpa-gs__cb--item">
    <div class="bpa-gs__cb--item-heading">
        <h4 class="bpa-sec--sub-heading"><?php esc_html_e('SMS Settings', 'bookingpress-sms'); ?></h4>
    </div>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row">
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="bookingpress_sms_err_msg != ''">
            <span class="bpa-sms-error-msg">{{ bookingpress_sms_err_msg }}</span>
        </el-col>
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="bookingpress_sms_success_msg != ''">
            <span class="bpa-sms-success-msg">{{ bookingpress_sms_success_msg }}</span>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row">
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <div class="bpa-gs__cb--item-heading">
                <h4 class="bpa-sec--sub-heading __bpa-sec--sub-heading-no-stroke __bpa-is-gs-heading-mb-0"><?php esc_html_e( 'API Configuration', 'bookingpress-sms' ); ?></h4>
            </div>
        </el-col>
    </el-row> 
    <el-row class="bpa-gs--tabs-pb__cb-item-row">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Select SMS Gateway', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bookingpress_selected_sms_gateway">
                 <el-select class="bpa-form-control" @change="bookingpress_sms_gateway_change" v-model="notification_setting_form.bookingpress_selected_sms_gateway" filterable placeholder="<?php esc_html_e('Select SMS Gateway', 'bookingpress-sms'); ?>">
                    <el-option key="select_sms_gateway" value="select_sms_gateway" label="<?php esc_html_e('Select SMS Gateway', 'bookingpress-sms'); ?>"></el-option>
                    <el-option v-for="item in bookingpress_sms_gateways" :key="item.name" :label="item.name" :value="item.name"></el-option>
                </el-select>
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Clickatell'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="clickatell_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.clickatell_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Clickatell'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="clickatell_api_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.clickatell_api_id" placeholder="<?php esc_html_e( 'API ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Redoxygen'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Account ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="redoxygen_account_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.redoxygen_account_id" placeholder="<?php esc_html_e( 'Enter username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Redoxygen'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Email', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="redoxygen_email">
                <el-input class="bpa-form-control" v-model="notification_setting_form.redoxygen_email" placeholder="<?php esc_html_e( 'Enter email', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Redoxygen'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="redoxygen_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.redoxygen_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === '1s2u'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="sms_1s2u_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.sms_1s2u_username" placeholder="<?php esc_html_e( 'Enter username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === '1s2u'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="sms_1s2u_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.sms_1s2u_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Experttexting'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="experttexting_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.experttexting_username" placeholder="<?php esc_html_e( 'Enter username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Experttexting'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="experttexting_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.experttexting_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Experttexting'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="experttexting_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.experttexting_api_key" placeholder="<?php esc_html_e( 'Enter API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Experttexting'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="experttexting_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.experttexting_sender_id" placeholder="<?php esc_html_e( 'Enter sender id/ mobile number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'BearSMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bearsms_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.bearsms_username" placeholder="<?php esc_html_e( 'Enter username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'BearSMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bearsms_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.bearsms_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'BearSMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'SMS Sender ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bearsms_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.bearsms_sender_id" placeholder="<?php esc_html_e( 'SMS Sender ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>   

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Spirius'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="spirius_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.spirius_username" placeholder="<?php esc_html_e( 'Enter username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Spirius'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="spirius_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.spirius_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Spirius'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="spirius_from_number">
                <el-input class="bpa-form-control" v-model="notification_setting_form.spirius_from_number" placeholder="<?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>   

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'D7 SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="d7sms_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.d7sms_username" placeholder="<?php esc_html_e( 'Enter username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'D7 SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="d7sms_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.d7sms_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'D7 SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Name', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="d7sms_sender_name">
                <el-input class="bpa-form-control" v-model="notification_setting_form.d7sms_sender_name" placeholder="<?php esc_html_e( 'Sender Name', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>   
   
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'EZTexting'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'User', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="eztexting_user">
                <el-input class="bpa-form-control" v-model="notification_setting_form.eztexting_user" placeholder="<?php esc_html_e( 'Enter User', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'EZTexting'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="eztexting_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.eztexting_password" placeholder="<?php esc_html_e( 'Enter password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <!-- RingCaptcha changes start -->
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'RingCaptcha'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Select Locale', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bookingpress_selected_ringcaptcha_locale">
                <!-- <el-select class="bpa-form-control" v-model="notification_setting_form.bookingpress_selected_sms_api_endpoint" filterable @change="app.$forceUpdate()"> -->
                <el-select class="bpa-form-control" v-model="notification_setting_form.bookingpress_selected_ringcaptcha_locale" filterable @change="app.$forceUpdate()">
                    <!-- <el-option v-for="item in bookingpress_sms_api_endpoint" :key="item.name" :label="item.name" :value="item.endpointurl"></el-option> -->
                    <el-option v-for="item in bookingpress_sms_ringcaptcha_locale" :key="item.name" :label="item.name" :value="item.ringcaptchalocale"></el-option>
                </el-select>
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'RingCaptcha'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'App Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="ringcaptcha_app_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.ringcaptcha_app_key" placeholder="<?php esc_html_e( 'App Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'RingCaptcha'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="ringcaptcha_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.ringcaptcha_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
        
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Bulksms'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bulksms_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.bulksms_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Bulksms'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bulksms_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.bulksms_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Exotel'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="exotel_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.exotel_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Exotel'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Token', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="exotel_api_token">
                <el-input class="bpa-form-control" v-model="notification_setting_form.exotel_api_token" placeholder="<?php esc_html_e( 'API Token', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Exotel'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sub Domain', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="exotel_sub_domain">
                <el-input class="bpa-form-control" v-model="notification_setting_form.exotel_sub_domain" placeholder="<?php esc_html_e( 'Sub Domain', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Exotel'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Account SID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="exotel_account_sid">
                <el-input class="bpa-form-control" v-model="notification_setting_form.exotel_account_sid" placeholder="<?php esc_html_e( 'Account SID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Exotel'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="exotel_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.exotel_sender_id" placeholder="<?php esc_html_e( 'Sender ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Text Local'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="textlocal_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.textlocal_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Text Local'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Name', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="textlocal_sender_name">
                <el-input class="bpa-form-control" v-model="notification_setting_form.textlocal_sender_name" placeholder="<?php esc_html_e( 'Sender Name', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Nexmo'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="nexmo_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.nexmo_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Nexmo'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Secret', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="nexmo_api_secret">
                <el-input class="bpa-form-control" v-model="notification_setting_form.nexmo_api_secret" placeholder="<?php esc_html_e( 'API Secret', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Nexmo'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender ID/Phone Number', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="nexmo_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.nexmo_sender_id" placeholder="<?php esc_html_e( 'Sender ID/Phone Number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'MSG91'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="msg91_auth_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.msg91_auth_key" placeholder="<?php esc_html_e( 'Auth Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'MSG91'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="msg91_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.msg91_sender_id" placeholder="<?php esc_html_e( 'Sender ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  


    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Textbelt'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="textbelt_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.textbelt_key" placeholder="<?php esc_html_e( 'Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Vianett'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="vianett_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.vianett_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Vianett'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="vianett_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.vianett_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Global'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'User', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsglobal_user">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsglobal_user" placeholder="<?php esc_html_e( 'User', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Global'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsglobal_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsglobal_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Global'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'MSIDSN/Sender ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsglobal_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsglobal_sender_id" placeholder="<?php esc_html_e( 'MSIDSN/Sender ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Text Marketer'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="textmarketer_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.textmarketer_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Text Marketer'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="textmarketer_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.textmarketer_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Text Marketer'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Originator', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="textmarketer_originator">
                <el-input class="bpa-form-control" v-model="notification_setting_form.textmarketer_originator" placeholder="<?php esc_html_e( 'Originator', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Swift SMS Gateway'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Account Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="swiftsms_account_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.swiftsms_account_key" placeholder="<?php esc_html_e( 'Account Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'MSG Club'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Auth Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="msgclub_auth_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.msgclub_auth_key" placeholder="<?php esc_html_e( 'Auth Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'MSG Club'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="msgclub_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.msgclub_sender_id" placeholder="<?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Easy Send Sms'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="easysendsms_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.easysendsms_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Easy Send Sms'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="easysendsms_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.easysendsms_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Easy Send Sms'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Name', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="easysendsms_sender_name">
                <el-input class="bpa-form-control" v-model="notification_setting_form.easysendsms_sender_name" placeholder="<?php esc_html_e( 'Sender Name', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Cheap Global SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Account No', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="cheapglobalsms_account_no">
                <el-input class="bpa-form-control" v-model="notification_setting_form.cheapglobalsms_account_no" placeholder="<?php esc_html_e( 'Account No', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Cheap Global SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Account Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="cheapglobalsms_account_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.cheapglobalsms_account_password" placeholder="<?php esc_html_e( 'Account Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Cheap Global SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="cheapglobalsms_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.cheapglobalsms_sender_id" placeholder="<?php esc_html_e( 'Sender ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Messente'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="messente_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.messente_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Messente'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="messente_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.messente_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Messente'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender name', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="messente_sender_name">
                <el-input class="bpa-form-control" v-model="notification_setting_form.messente_sender_name" placeholder="<?php esc_html_e( 'Sender name', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Message Bird'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Authorization Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="messagebird_authorization_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.messagebird_authorization_key" placeholder="<?php esc_html_e( 'Authorization Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Message Bird'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Originator', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="messagebird_originator">
                <el-input class="bpa-form-control" v-model="notification_setting_form.messagebird_originator" placeholder="<?php esc_html_e( 'Originator', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Telnyx'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Profile Secret', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="telnyx_profile_secret">
                <el-input class="bpa-form-control" v-model="notification_setting_form.telnyx_profile_secret" placeholder="<?php esc_html_e( 'Profile Secret', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Telnyx'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="telnyx_from_number">
                <el-input class="bpa-form-control" v-model="notification_setting_form.telnyx_from_number" placeholder="<?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Routee'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Application ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="routee_application_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.routee_application_id" placeholder="<?php esc_html_e( 'Application ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Routee'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Application Secret', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="routee_application_secret">
                <el-input class="bpa-form-control" v-model="notification_setting_form.routee_application_secret" placeholder="<?php esc_html_e( 'Application Secret', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Routee'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'From', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="routee_from_number">
                <el-input class="bpa-form-control" v-model="notification_setting_form.routee_from_number" placeholder="<?php esc_html_e( 'From', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Wavecell'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Authorization', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="wavecell_authorization">
                <el-input class="bpa-form-control" v-model="notification_setting_form.wavecell_authorization" placeholder="<?php esc_html_e( 'Authorization', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Wavecell'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sub Account ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="wavecell_sub_account_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.wavecell_sub_account_id" placeholder="<?php esc_html_e( 'Sub Account ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Wavecell'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Source (Sender ID)', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="wavecell_source">
                <el-input class="bpa-form-control" v-model="notification_setting_form.wavecell_source" placeholder="<?php esc_html_e( 'Source (Sender ID)', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'EngageSPARK'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Authorization', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="engagespark_authorization">
                <el-input class="bpa-form-control" v-model="notification_setting_form.engagespark_authorization" placeholder="<?php esc_html_e( 'Authorization', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'EngageSPARK'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Org ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="engagespark_orgid">
                <el-input class="bpa-form-control" v-model="notification_setting_form.engagespark_orgid" placeholder="<?php esc_html_e( 'Org ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'EngageSPARK'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender ID/Phone Number', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="engagespark_senderid">
                <el-input class="bpa-form-control" v-model="notification_setting_form.engagespark_senderid" placeholder="<?php esc_html_e( 'Sender ID/Phone Number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Gateway Center'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsgatewaycenter_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsgatewaycenter_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Gateway Center'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'User Id', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsgatewaycenter_user_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsgatewaycenter_user_id" placeholder="<?php esc_html_e( 'User Id', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Gateway Center'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsgatewaycenter_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsgatewaycenter_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS Gateway Center'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsgatewaycenter_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsgatewaycenter_sender_id" placeholder="<?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <!-- add new dropdown -->
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS API'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Select SMS API', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bookingpress_selected_sms_api_endpoint">
                <el-select class="bpa-form-control" v-model="notification_setting_form.bookingpress_selected_sms_api_endpoint" filterable @change="app.$forceUpdate()">
                    <el-option v-for="item in bookingpress_sms_api_endpoint" :key="item.name" :label="item.name" :value="item.endpointurl"></el-option>
                </el-select>
            </el-form-item>
        </el-col>
    </el-row>    

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS API'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Access Token', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsapi_access_token">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsapi_access_token" placeholder="<?php esc_html_e( 'Access Token', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'SMS API'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="smsapi_from_number">
                <el-input class="bpa-form-control" v-model="notification_setting_form.smsapi_from_number" placeholder="<?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Fast 2 SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="fast2sms_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.fast2sms_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Fast 2 SMS'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="fast2sms_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.fast2sms_sender_id" placeholder="<?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Alcodes'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="alcodes_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.alcodes_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Alcodes'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="alcodes_sender_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.alcodes_sender_id" placeholder="<?php esc_html_e( 'Sender Id', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>     

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Movile'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="movile_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.movile_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Movile'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="movile_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.movile_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>     

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Twilio'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Account SID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="twilio_account_sid">
                <el-input class="bpa-form-control" v-model="notification_setting_form.twilio_account_sid" placeholder="<?php esc_html_e( 'Account SID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Twilio'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Auth Token', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="twilio_auth_token">
                <el-input class="bpa-form-control" v-model="notification_setting_form.twilio_auth_token" placeholder="<?php esc_html_e( 'Auth Token', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>     
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Twilio'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="twilio_from_number">
                <el-input class="bpa-form-control" v-model="notification_setting_form.twilio_from_number" placeholder="<?php esc_html_e( 'From Number', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>     
    
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Telerivet'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="telerivet_api_key">
                <el-input class="bpa-form-control" v-model="notification_setting_form.telerivet_api_key" placeholder="<?php esc_html_e( 'API Key', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'Telerivet'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Project ID', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="telerivet_project_id">
                <el-input class="bpa-form-control" v-model="notification_setting_form.telerivet_project_id" placeholder="<?php esc_html_e( 'Project ID', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'ClickSend'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Username', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="clicksend_username">
                <el-input class="bpa-form-control" v-model="notification_setting_form.clicksend_username" placeholder="<?php esc_html_e( 'Username', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>      
    <el-row class="bpa-gs--tabs-pb__cb-item-row" v-if="notification_setting_form.bookingpress_selected_sms_gateway === 'ClickSend'">
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Password', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="clicksend_password">
                <el-input class="bpa-form-control" v-model="notification_setting_form.clicksend_password" placeholder="<?php esc_html_e( 'Password', 'bookingpress-sms' ); ?>"></el-input>	
            </el-form-item>
        </el-col>
    </el-row>  

    <el-row class="bpa-gs--tabs-pb__cb-item-row" >
        <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
            <h4><?php esc_html_e( 'Select Moblie Number Field', 'bookingpress-sms' ); ?></h4>
        </el-col> 
        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
            <el-form-item prop="bookingpress_selected_phone_number_field">
                <el-select class="bpa-form-control" v-model="notification_setting_form.bookingpress_selected_phone_number_field" filterable placeholder="<?php esc_html_e('Select Mobile Number Field', 'bookingpress-sms'); ?>">
                    <el-option v-for="item in bookingpress_form_fields_data" :label="item.bookingpress_field_label" :value="item.bookingpress_field_meta_key"></el-option>
                </el-select>
            </el-form-item>
        </el-col>
    </el-row>  
    <el-form :model="bookingpress_test_sms_form" :rules="bookingpress_test_sms_rules" ref="bookingpress_test_sms_form">
        <el-row class="bpa-gs--tabs-pb__cb-item-row">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <div class="bpa-gs__cb--item-heading">
                    <h4 class="bpa-sec--sub-heading __bpa-sec--sub-heading-no-stroke __bpa-is-gs-heading-mb-0"><?php esc_html_e( 'SMS Testing', 'bookingpress-sms' ); ?></h4>
                </div>
            </el-col>
        </el-row>
        <el-row class="bpa-gs--tabs-pb__cb-item-row">
            <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
                <h4><?php esc_html_e( 'To Number', 'bookingpress-sms' ); ?></h4>
            </el-col> 
            <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                <el-form-item prop="test_to_number">
                    <el-input v-model="bookingpress_test_sms_form.test_to_number" class="bpa-form-control" placeholder="<?php esc_html_e( 'To Number', 'bookingpress-sms' ); ?>"></el-input>                 
                </el-form-item>
            </el-col>
        </el-row>         
        <el-row class="bpa-gs--tabs-pb__cb-item-row bpa-gs--tabs-pb__cb-item-row--is-mb-true">
            <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
                <h4><?php esc_html_e( 'Enter Message', 'bookingpress-sms' ); ?></h4>
            </el-col> 
            <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                <el-form-item prop="test_to_msg">
                    <el-input v-model="bookingpress_test_sms_form.test_to_msg" class="bpa-form-control" type="textarea"></el-input>            
                </el-form-item>
            </el-col>
        </el-row>
        <el-row class="bpa-gs--tabs-pb__cb-item-row">
            <el-col :xs="{span: 12, offset: 12}" :sm="{span: 12, offset: 12}" :md="{span: 12, offset: 12}" :lg="{span: 16, offset: 8}" :xl="{span: 16, offset: 8}">            
                <el-button class="bpa-btn bpa-btn__medium bpa-btn--primary el-button--default" :class="(is_display_send_sms_loader == '1') ? 'bpa-btn--is-loader' : ''" @click="bookingpress_send_test_sms('bookingpress_test_sms_form')">
                    <span class="bpa-btn__label"><?php esc_html_e( 'Send Test SMS', 'bookingpress-sms' ); ?></span>
                    <div class="bpa-btn--loader__circles">				    
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </el-button>
            </el-col>
        </el-row>
    </el-form>
</div>