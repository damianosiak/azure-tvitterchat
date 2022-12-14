/****** Object:  Table [dbo].[dislikes]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[dislikes](
	[post] [int] NOT NULL,
	[userid] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[dislikes_bck]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[dislikes_bck](
	[post] [int] NOT NULL,
	[userid] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[likes]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[likes](
	[post] [int] NOT NULL,
	[userid] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[likes_bck]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[likes_bck](
	[post] [int] NOT NULL,
	[userid] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[posts]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[posts](
	[pid] [int] IDENTITY(1,1) NOT NULL,
	[userid] [int] NOT NULL,
	[date] [datetime] NOT NULL,
	[content] [varchar](512) NOT NULL,
	[likes] [int] NOT NULL,
	[dislikes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[pid] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[posts_bck]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[posts_bck](
	[pid] [int] IDENTITY(1,1) NOT NULL,
	[userid] [int] NOT NULL,
	[date] [datetime] NOT NULL,
	[content] [varchar](512) NOT NULL,
	[likes] [int] NOT NULL,
	[dislikes] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sessions]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sessions](
	[sid] [varchar](16) NOT NULL,
	[userid] [int] NOT NULL,
	[date] [datetime] NOT NULL,
UNIQUE NONCLUSTERED 
(
	[sid] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sessions_bck]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sessions_bck](
	[sid] [varchar](16) NOT NULL,
	[userid] [int] NOT NULL,
	[date] [datetime] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[transfers]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[transfers](
	[tid] [int] IDENTITY(1,1) NOT NULL,
	[date] [datetime] NOT NULL,
	[userid] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[tid] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[transfers_bck]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[transfers_bck](
	[tid] [int] IDENTITY(1,1) NOT NULL,
	[date] [datetime] NOT NULL,
	[userid] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[users]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[users](
	[uid] [int] IDENTITY(1,1) NOT NULL,
	[login] [varchar](32) NOT NULL,
	[password] [varchar](96) NOT NULL,
	[email] [varchar](64) NOT NULL,
	[locked] [tinyint] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[uid] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[users_bck]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[users_bck](
	[uid] [int] IDENTITY(1,1) NOT NULL,
	[login] [varchar](32) NOT NULL,
	[password] [varchar](96) NOT NULL,
	[email] [varchar](64) NOT NULL,
	[locked] [tinyint] NOT NULL
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[posts] ADD  DEFAULT ((0)) FOR [likes]
GO
ALTER TABLE [dbo].[posts] ADD  DEFAULT ((0)) FOR [dislikes]
GO
ALTER TABLE [dbo].[users] ADD  DEFAULT ((0)) FOR [locked]
GO
/****** Object:  StoredProcedure [dbo].[p_addDislike]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   PROCEDURE [dbo].[p_addDislike](@ipost int, @iuser int, @retval tinyint)
AS
BEGIN
	DECLARE @amount_dl int=0;
	DECLARE @amount_l int=0;
	set @amount_dl=(select isnull(count(post),0) from dislikes where post=@ipost and userid=@iuser);
	set @amount_l=(select isnull(count(post),0) from likes where post=@ipost and userid=@iuser);
	
    IF @amount_dl<=0
	BEGIN
    	update posts set dislikes=dislikes+1 where pid=@ipost;
        set @retval=1;
        insert into dislikes (post, userid) values (@ipost, @iuser);
	END
    ELSE
	BEGIN
    	set @retval=0;
	END


	IF @amount_l>0
	BEGIN
		update posts set likes=likes-1 where pid=@ipost;
		delete from likes where post=@ipost and userid=@iuser;
	END
END 
GO
/****** Object:  StoredProcedure [dbo].[p_addLike]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   PROCEDURE [dbo].[p_addLike](@ipost int, @iuser int, @retval tinyint)
AS
BEGIN
	DECLARE @amount_dl int=0;
	DECLARE @amount_l int=0;
	set @amount_dl=(select isnull(count(post),0) from dislikes where post=@ipost and userid=@iuser);
	set @amount_l=(select isnull(count(post),0) from likes where post=@ipost and userid=@iuser);
	
    IF @amount_l=0
	BEGIN
    	update posts set likes=likes+1 where pid=@ipost;
        set @retval=1;
        insert into likes (post, userid) values (@ipost, @iuser);
	END
    ELSE
	BEGIN
    	set @retval=0;
    END;

	IF @amount_dl>0
	BEGIN
		update posts set dislikes=dislikes-1 where pid=@ipost;
		delete from dislikes where post=@ipost and userid=@iuser;
	END;	
END
GO
/****** Object:  StoredProcedure [dbo].[p_removeDislike]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   PROCEDURE [dbo].[p_removeDislike](@ipost int, @iuser int, @retval tinyint OUTPUT)
AS
BEGIN
	DECLARE @amount_dl int=0;
	set @amount_dl=(select isnull(count(post),0) from dislikes where post=@ipost and user=@iuser);

	IF @amount_dl>0
	BEGIN
		update posts set dislikes=dislikes-1 where pid=@ipost;
		set @retval=1;
    	delete from dislikes where post=@ipost and user=@iuser;
	END
	ELSE
	BEGIN
		set @retval=0;
	END
END
GO
/****** Object:  StoredProcedure [dbo].[p_removeLike]    Script Date: 12/11/2022 8:19:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE   PROCEDURE [dbo].[p_removeLike](@ipost int, @iuser int, @retval tinyint)
AS
BEGIN
	DECLARE @amount_l int=0;
	set @amount_l=(select isnull(count(post),0) from likes where post=@ipost and user=@iuser);
	
    IF @amount_l>0
	BEGIN
    	update posts set likes=likes-1 where pid=@ipost;
        set @retval=1;
        delete from likes where post=@ipost and user=@iuser;
	END
    ELSE
	BEGIN
		set @retval=0;
	END;
END
GO
